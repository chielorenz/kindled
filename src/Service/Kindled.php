<?php

namespace App\Service;

use App\Service\Mercury;
use Twig\Environment as Twig;
use GuzzleHttp\Client as Guzzle;
use GuzzleHttp\Exception\GuzzleException;
use Symfony\Component\DomCrawler\Crawler;

/**
 * This class works with HTML files and converts them into .mobi files
 */
class Kindled
{
	/**
	 * @var string
	 */
	private $folder;

	/**
	 * @var string
	 */
	private $kindlegen;

    /**
     * @var Mercury
     */
    private $mercury;

    /**
     * @var Guzzle
     */
    private $guzzle;

    /**
     * @var Twig
     */
    private $twig;

    /**
     * @param string $folder Temporarly folder where to put the files
     * @param string $kindlegen Path to the Kindlegen binary
     * @param Mercury $mercury
     * @param Twig $twig
     * @param Guzzle $guzzle
     */
    public function __construct($folder, $kindlegen, Mercury $mercury, Twig $twig, Guzzle $guzzle)
    {
    	$this->folder = $folder;
    	$this->kindlegen = $kindlegen;
        $this->mercury = $mercury;
        $this->twig = $twig;
    	$this->guzzle = $guzzle;
    }

    /**
     * Convert an HTML page to .mobi
     * 
     * @param string  $url Url of the page to convert
     * @return string  Path to the .mobi file
     */
    public function convert($url) : string
    {
    	$title = uniqid();
		$html = $this->folder . $title . '.html';
        $mobi = $this->folder . $title . '.mobi';
        $log = $this->folder . $title . '.log';

        if (!is_dir($this->folder)) {
            mkdir($this->folder, 0777, true);
        }

        $mercury = $this->mercury->parse($url);
        $content = $this->twig->render('mobi/article.html.twig', $mercury);
        $content = $this->sanitize($content);
        $content = $this->encode($content);
    
        file_put_contents($html, $content);
        exec($this->kindlegen . ' ' . $html . ' -verbose > ' . $log);

		if (!file_exists($mobi)) {
            throw new \Exception('Unable to create the file: ' . file_get_contents($log));
        }

    	return $mobi;
    }

    /**
     * Clear all temp data
     */
    public function clear() 
    {
    	$files = glob($this->folder.'*');
        foreach($files as $file) {
            if(is_file($file)) {
                unlink($file);
            }
        }
    }

    /**
     * Set the right encoding
     * 
     * @param string  $html
     * @return string
     */
    private function encode(string $html) : string
    {
        try {
            return iconv('UTF-8', 'ISO-8859-1//TRANSLIT', $html);
        } catch(\Exception $e) {
            return  iconv('UTF-8', 'ISO-8859-1//IGNORE', $html);
        }
    }

    /**
     * Sanitize an html file to be .mobi ready
     * 
     * @param string  $html
     * @return string
     */
    private function sanitize($html) : string
    {                    
        $crawler = new Crawler($html);
        $crawler = $this->filterTags($crawler);
        $crawler = $this->fetchImages($crawler);
        return $crawler->html();
    }

    /**
     * Remove tags: script, meta, input select, textarea
     * 
     * @param Crawler  $crawler
     * @return Crawler  $crawler
     */
    private function filterTags(Crawler $crawler) : Crawler
    {
    	$crawler->filter('script, meta, input, select, textarea, link')->each(function ($crawler) {
    	    foreach ($crawler as $node) {
                $node->parentNode->removeChild($node);
            }
        });

    	return $crawler;
    }

    /**
     * Make remove image local
     * 
     * @param Crawler  $crawler
     * @return Crawler  $crawler
     */
    private function fetchImages(Crawler $crawler) : Crawler
    {
    	$guzzle = $this->guzzle;
    	
    	$crawler->filter('img')->each(function ($crawler) use ($guzzle) {
    	    foreach ($crawler as $node) {
                $class = $node->getAttribute('class');
                $src = $node->getAttribute('src');
                if($src) {
                    $image = $guzzle->request('get', $src)->getBody();
                    $extension = pathinfo(parse_url($src)['path'], PATHINFO_EXTENSION);
                    $file = uniqid().'.'.$extension;
                    file_put_contents($this->folder.$file, $image);

                    $dom = $node->ownerDocument;
                    $elem = $dom->createElement('img');
                    $elem->setAttribute('class', $class);
                    $elem->setAttribute('src', './'.$file);
                    $node->parentNode->replaceChild($elem, $node);
                } else {
                    $node->parentNode->removeChild($node);
                }
            }
        });	

    	return $crawler;
    }
}