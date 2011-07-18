<?php

include_once('simple_html_dom.php');

$articles = (array($_POST[article0], $_POST[article1], $_POST[article2], $_POST[article3], $_POST[article4])) ;
$galleries = (array($_POST[gallery0], $_POST[gallery1], $_POST[gallery2])) ;


$i=0;
foreach ($articles as $node) {
	$article[$i] = scrapearticle($node);
	$i++;
}

$i=0;
foreach ($galleries as $node) {
	$gallery[$i] = scrapegallery($node);
	$i++;
}

function convert_smart_quotes($string) 
{ 
    $search = array(chr(145), 
                    chr(146), 
					chr(212),
					chr(213),
                    chr(147),
                    chr(148), 
                    chr(151),
					chr(0xe2) . chr(0x80) . chr(0x98),
                  chr(0xe2) . chr(0x80) . chr(0x99),
                  chr(0xe2) . chr(0x80) . chr(0x9c),
                  chr(0xe2) . chr(0x80) . chr(0x9d),
                  chr(0xe2) . chr(0x80) . chr(0x93),
                  chr(0xe2) . chr(0x80) . chr(0x94)); 
 
    $replace = array("'", 
                     "'", 
                     "'", 
                     "'", 
                     '"', 
                     '"', 
                     '--',
                     "'", 
                     "'", 
                     '"', 
                     '"', 
                     '--',
                     '-'); 
 
    return str_replace($search, $replace, $string); 
} 

function charset_decode_utf_8 ($string) {
      /* Only do the slow convert if there are 8-bit characters */
    /* avoid using 0xA0 (\240) in ereg ranges. RH73 does not like that */
    if (! ereg("[\200-\237]", $string) and ! ereg("[\241-\377]", $string))
        return $string;

    // decode three byte unicode characters
    $string = preg_replace("/([\340-\357])([\200-\277])([\200-\277])/e",       
    "'&#'.((ord('\\1')-224)*4096 + (ord('\\2')-128)*64 + (ord('\\3')-128)).';'",   
    $string);

    // decode two byte unicode characters
    $string = preg_replace("/([\300-\337])([\200-\277])/e",
    "'&#'.((ord('\\1')-192)*64+(ord('\\2')-128)).';'",
    $string);

    return $string;
} 

// scrape articles

function scrapearticle($node) {

	if (empty($node)) {
		$copy[hed] = "[HED GOES HERE]";
		$copy[dek] = "Lorem ipsum dolor sit amet, consectetur intellegant altera. Altera iudicii iucunde videtur ingeniis philosophiam!";
		$copy[img] = "[IMAGE NAME GOES HERE]";
		$copy[url] = "[URL GOES HERE]";
		$copy[cat] = "[CATEGORY HERE]";
		return $copy;
		 }

	$url = "http://popsci.com/node/$node";

	$html = file_get_html($url);

	$hed = $html->find('h1[class=title]'); 
	$dek = $html->find('div[class=dek]');

//	$image = $html->find('div.associations img');

//	if (!isset($image)) {
		$image = $html->find('div.image-center img');
//	}
	$imagesrc = $image[0]->src;
	$imageroot = basename($imagesrc);

	$meta = $html->find('meta[name=keywords]');
	$keywords = $meta[0]->content;
	$category = explode(",", $keywords);	

	$hed = convert_smart_quotes(trim((strip_tags($hed[0]))));
	$dek = convert_smart_quotes(trim((strip_tags($dek[0]))));

//	$urlbase = $html->find('span[class=comments] a');
//	$urlhref = explode("?",$urlbase[0]->href);
//	$url = "http://popsci.com" . $urlhref[0];

	$copy = array(hed => $hed, dek => $dek, img => $imageroot, cat => strtoupper($category[0]), url => $url);

	return $copy;

}





// scrape galleries

function scrapegallery ($node) {

	if (empty($node)) {
		$copy[hed] = "[HED GOES HERE]";
		$copy[dek] = "Lorem ipsum dolor sit amet, consectetur intellegant altera. Altera iudicii iucunde videtur ingeniis philosophiam!";
		$copy[img] = "[IMAGE NAME GOES HERE]";
		$copy[url] = "[URL GOES HERE]";
		$copy[cat] = "[CATEGORY HERE]";
		return $copy;
		 }


	$url = "http://popsci.com/node/$node";

	$html = file_get_html($url);

	$hed = $html->find('h1[class=title]'); 
	$dek = $html->find('div[class=dek]');

	//$image = $html->find('ul[id=nc_image_gallery_scroll] img');
	$image = $html->find('div[class=gallery_image_wrapper] img');
	$imagesrc = $image[0]->src;
	$imageroot = basename($imagesrc);

	$meta = $html->find('meta[name=keywords]');
	$keywords = $meta[0]->content;
	$category = explode(",", $keywords);	

	$hed = convert_smart_quotes(trim((strip_tags($hed[0]))));
	$dek = convert_smart_quotes(trim((strip_tags($dek[0]))));

//	$urlbase = $html->find('span[class=comments] a');
//	$urlhref = explode("#",$urlbase[0]->href);
//	$url = "http://popsci.com" . $urlhref[0];

	$copy = array(hed => $hed, dek => $dek, img => $imageroot, cat => strtoupper($category[0]), url => $url);

	return $copy;

}

$datetag = date("mdy", strtotime("+1 Thursday"));

$tag = "/?cmpid=enews$datetag";

$snippet = $_POST[snippet];
	
$htmlnewsletter = <<<EONL



<html>
    <head>
        <title>PopSci.com Newsletter</title>
    </head>
    <body>
        <center>
        <table width="728" cellspacing="5" cellpadding="5" border="0" align="center">
            <tbody>
                <tr>
                    <td height="25" width="548" align="left"><font face="Arial, Helvetica, sans-serif" style="font-weight: normal; font-size: 11px; color: rgb(0, 0, 0); line-height: 20px;"><a name="$snippet" href="http://www.popsci.com/$tag" style="color: rgb(0, 0, 0); text-decoration: underline;" xtlinkname="Snippet Text" xt="SPCLICK" target="_blank">$snippet</a></font></td>
                    <td width="145" align="right"><font face="Arial, Helvetica, sans-serif" style="font-weight: normal; font-size: 11px; color: rgb(0, 0, 0); line-height: 20px;">View this email <a name="Online" href="http://recp.rm05.net/servlet/MailView?m=%%MAILING_ID%%&r=%%RECIPIENT_ID%%&j=%%JOB_ID_CODE%%&mt=1" style="color: rgb(0, 0, 0); text-decoration: underline;" xtlinkname="Online" xt="SPCLICK" target="_blank">online</a></font></td>
                </tr>
            </tbody>
        </table>
        <table width="600" cellspacing="0" cellpadding="0" border="0" align="center">
            <tbody>
                <tr>
                    <td valign="top" align="center" colspan="2"></td>
                </tr>
                <tr>
                    <td valign="middle" height="90" align="center" colspan="2">
                    
<a href="http://ad.doubleclick.net/jump/n6747.popsci/newsletter;pos=x60;sz=728x90;topic=technology;tile=1;ord=23456781?" target="_blank">

<img src="http://ad.doubleclick.net/ad/n6747.popsci/newsletter;pos=x60;sz=728x90;topic=technology;tile=1;ord=23456781?" width="728" height="90" border="0" alt="">

</a>
                    
                    <div align="center"></div>                    </td>
								</tr>
                <tr>
                    <td valign="middle" height="77" align="center" colspan="2"><a target="_blank" xtlinkname="Popular Science" xt="SPCLICK" href="http://www.popsci.com/?cmpid=PSCenews" name="Popular Science"><img height="84" width="790" border="0" title="POPSCI.COM NEWSLETTER" alt="POPSCI.COM NEWSLETTER" name="newsletterheader2.gif" xt="SPIMAGE" contentid="a0a82c-12512194260-3f3d5eceea4051b7c82d96ba93c1b04e" style="margin: 0px; padding: 0px; display:block&nbsp;border:none;" src="newsletterheader2.gif" /></a></td>

                </tr>								
                <tr>
                    <td valign="top" align="left" style="border-style: solid; border-color: 
rgb(222, 222, 222); border-width: 10px 0px 0px 10px; padding: 20px 0px 20px 20px; width: 
378px;"><a name="Feature Image" href="{$article[0][url]}$tag" xtlinkname="Feature Image" 
xt="SPCLICK" target="_blank"><img height="250" width="400" border="0" 
src="http://www.popsci.com/files/imagecache/newsletter_image_large/files/articles/{$article[0][img]}" 
style="border: 1px solid rgb(0, 104, 165); margin: 0px; padding-bottom: 0px; padding-top: 0px;" spname="http:__www.popsci.com_files_imagecache_article_image_large_files_articles_{$article[0][img]}" name="http:__www.popsci.com_files_imagecache_article_image_large_files_articles_{$article[0][img]}" alt="Feature Image" title="Feature Image" /></a>
                    <p align="left"><span style="font-weight: bold; font-size: 11px; text-transform: uppercase; color: rgb(237, 28, 36); line-height: 20px;">{$article[0][cat]} <br />
                    </span><a name="Feature Title" href="{$article[0][url]}$tag" style="font-weight: bold; font-size: 26px; color: rgb(0, 104, 165); text-decoration: none;" xtlinkname="Feature Title" xt="SPCLICK" target="_blank">{$article[0][hed]} <br />
                    </a><!--dek-->{$article[0][dek]}<br />
                    <a name="Feature Read Full Story" href="{$article[0][url]}$tag" style="font-weight: bold; font-size: 11px; text-transform: uppercase; color: rgb(0, 104, 165); line-height: 25px; text-decoration: none;" xtlinkname="Feature Read Full Story" xt="SPCLICK" target="_blank" title="read full story">[read full story]</a></p>
                    <p style="border-bottom: 1px solid rgb(222, 222, 222); margin: 0px; padding: 20px 0px;"></p>
                    <img height="21" width="360" title="This Week's Top Photo Galleries" alt="This Week's Top Photo Galleries" name="topphotogalleries.jpg" xt="SPIMAGE" contentid="a0a82c-125121942ef-3f3d5eceea4051b7c82d96ba93c1b04e" style="padding-bottom: 0px; margin-left: 0px; margin-right: 10px; padding-top: 0px;" src="topphotogalleries.jpg" />
                    <div align="left" style="padding: 10px 10px 10px 0px; float: left; width: 115px;"><a name="Photo Gallery 1a" href="{$gallery[0][url]}$tag" style="border: medium none rgb(0, 104, 165);" xtlinkname="Photo Gallery 1a" xt="SPCLICK" target="_blank"><img height="98" width="98" src="http://www.popsci.com/files/imagecache/photogallery_thumbnails_for_block/files/articles/{$gallery[0][img]}" style="border: 1px solid rgb(0, 104, 165); margin: 0px; padding: 0px;" spname="http:__www.popsci.com_files_imagecache_photogallery_thumbnails_for_block_files_articles_{$gallery[0][img]}" name="http:__www.popsci.com_files_imagecache_photogallery_thumbnails_for_block_files_articles_{$gallery[0][img]}" alt="Photo Gal - image" title="Photo Gal - image" /></a>
                    <p><a name="Photo Gallery 1b" href="{$gallery[0][url]}$tag" style="font-weight: bold; font-size: 13px; color: rgb(0, 104, 165); text-decoration: none;" xtlinkname="Photo Gallery 1b" xt="SPCLICK" target="_blank">{$gallery[0][hed]}<br />
                    </a><!--dek-->{$gallery[0][dek]}</p>
                    </div>
                    <div align="left" style="padding: 10px 10px 10px 0px; float: left; width: 115px;"><a name="Photo Gallery 2a" href="{$gallery[1][url]}$tag" style="border: medium none rgb(0, 104, 165);" xtlinkname="Photo Gallery 2a" xt="SPCLICK" target="_blank"><img height="98" width="98" src="http://www.popsci.com/files/imagecache/photogallery_thumbnails_for_block/files/articles/{$gallery[1][img]}" style="border: 1px solid rgb(0, 104, 165); margin: 0px; padding: 0px;" spname="http:__www.popsci.com_files_imagecache_photogallery_thumbnails_for_block_files_articles_{$gallery[1][img]}" name="http:__www.popsci.com_files_imagecache_photogallery_thumbnails_for_block_files_articles_{$gallery[1][img]}" alt="Photo Gal - image" title="Photo Gal - image" /></a>
                    <p><a name="Photo Gallery 2b" href="{$gallery[1][url]}$tag" style="font-weight: bold; font-size: 13px; color: rgb(0, 104, 165); text-decoration: none;" xtlinkname="Photo Gallery 2b" xt="SPCLICK" target="_blank">{$gallery[1][hed]}<br />
                    </a><!--dek-->{$gallery[1][dek]}</p>
                    </div>
                    <div align="left" style="padding: 10px 10px 10px 0px; float: left; width: 115px;"><a name="Photo Gallery 3a" href="{$gallery[2][url]}$tag" style="border: medium none rgb(0, 104, 165);" xtlinkname="Photo Gallery 3a" xt="SPCLICK" target="_blank"><img height="98" width="98" src="http://www.popsci.com/files/imagecache/photogallery_thumbnails_for_block/files/articles/{$gallery[2][img]}" style="border: 1px solid rgb(0, 104, 165); margin: 0px; padding: 0px;" spname="http:__www.popsci.com_files_imagecache_photogallery_thumbnails_for_block_files_articles_{$gallery[2][img]}" name="http:__www.popsci.com_files_imagecache_photogallery_thumbnails_for_block_files_articles_{$gallery[2][img]}" alt="Photo Gal - image" title="Photo Gal - image" /></a>
                    <p><a name="Photo Gallery 3b" href="{$gallery[2][url]}$tag" style="font-weight: bold; font-size: 13px; color: rgb(0, 104, 165); text-decoration: none;" xtlinkname="Photo Gallery 3b" xt="SPCLICK" target="_blank">{$gallery[2][hed]}<br />
                    </a><!--dek-->{$gallery[2][dek]}</p>
                    </div>
                    <p></p>
                    <p style="clear: both; font-weight: bold;">+ <a name="Photo Galleries" href="http://www.popsci.com/galleries/$tag" style="color: rgb(0, 104, 165); text-decoration: none;" xtlinkname="Photo Galleries" xt="SPCLICK" target="_blank">Photo Galleries</a></p>                    </td>
                    <td valign="top" background="http://www.thedesignprojects.com/dev/popsci/newsletter/images/stripe.gif" align="left" style="border-style: solid; border-color: rgb(222, 222, 222); border-width: 10px 10px 0px 0px; padding: 20px 20px 20px 40px; Width: 250px;" spname="http:__www.thedesignprojects.com_dev_popsci_newsletter_images_stripe.gif" name="http:__www.thedesignprojects.com_dev_popsci_newsletter_images_stripe.gif"><img height="21" width="300" border="0" alt="" name="whatshot35.jpg" xt="SPIMAGE" contentid="a0a82c-1251219430d-3f3d5eceea4051b7c82d96ba93c1b04e" spname="whatshot35.jpg" src="whatshot35.jpg" />
                    <p style="border-bottom: 1px solid rgb(222, 222, 222); margin: 0px; 
padding: 20px 0px;"><a name="Whats Hot 1 Image" href="{$article[1][url]}$tag" 
xtlinkname="Whats Hot 1 Image" xt="SPCLICK" target="_blank"><img hspace="5" height="150" 
width="240" border="0" 
src="http://www.popsci.com/files/imagecache/newsletter_image_small/files/articles/{$article[1][img]}" 
style="border: 1px solid rgb(0, 104, 165); padding: 0px;" spname="http:__www.popsci.com_files_imagecache_article_image_large_files_articles_{$article[1][img]}" name="http:__www.popsci.com_files_imagecache_article_image_large_files_articles_{$article[1][img]}" alt="What's Hot 1 Image" title="What's Hot 1 Image" /></a><span style="font-weight: bold; font-size: 11px; text-transform: uppercase; color: rgb(237, 28, 36); line-height: 20px;"><br />
                    {$article[1][cat]}<br />
                    </span><a name="Whats Hot 1 Title" href="{$article[1][url]}$tag" style="font-weight: bold; font-size: 13px; color: rgb(0, 104, 165); text-decoration: none;" xtlinkname="Whats Hot 1 Title" xt="SPCLICK" target="_blank">{$article[1][hed]} <br />
                    </a><!--dek-->{$article[1][dek]} <br />
                    <span style="font-weight: bold; font-size: 11px; text-transform: uppercase;">[<a name="Whats Hot 1 Read Full Story" href="{$article[1][url]}$tag" style="color: rgb(0, 104, 165); line-height: 25px; text-decoration: none;" xtlinkname="Whats Hot 1 Read Full Story" xt="SPCLICK" target="_blank">Read full story</a>]</span></p>


 <p style="border-bottom: 1px solid rgb(222, 222, 222); margin: 0px; padding: 20px 0px;"><a name="Whats Hot 2 Image" href="{$article[2][url]}$tag" xtlinkname="Whats Hot 2 Image" xt="SPCLICK" target="_blank"><img hspace="5" height="98" width="98" border="0" src="http://www.popsci.com/files/imagecache/photogallery_thumbnails_for_block/files/articles//{$article[2][img]}" style="border: 1px solid rgb(0, 104, 165); padding: 0px; float: left;" spname="http:__www.popsci.com_files_imagecache_photogallery_thumbnails_for_block_files_articles_{$article[2][img]}" name="http:__www.popsci.com_files_imagecache_photogallery_thumbnails_for_block_files_articles_{$article[2][img]}" alt="What's Hot 2 Image" title="What's Hot 2 Image" /></a><span style="font-weight: bold; font-size: 11px; text-transform: uppercase; color: rgb(237, 28, 36); line-height: 20px;"> {$article[2][cat]} <br />
                    </span><a name="Whats Hot 2 Title" href="{$article[2][url]}$tag" style="font-weight: bold; font-size: 13px; color: rgb(0, 104, 165); text-decoration: none;" xtlinkname="Whats Hot 2 Title" xt="SPCLICK" target="_blank">{$article[2][hed]} <br />
                    </a><!--dek-->{$article[2][dek]} <br />
                    <span style="font-weight: bold; font-size: 11px; text-transform: uppercase;">[<a name="Whats Hot 2 Read Full Story" href="{$article[2][url]}$tag" style="color: rgb(0, 104, 165); line-height: 25px; text-decoration: none;" xtlinkname="Whats Hot 2 Read Full Story" xt="SPCLICK" target="_blank">Read full story</a>]</span></p>

 <p style="border-bottom: 1px solid rgb(222, 222, 222); margin: 0px; padding: 20px 0px;"><a name="Whats Hot 3 Image" href="{$article[3][url]}$tag" xtlinkname="Whats Hot 3 Image" xt="SPCLICK" target="_blank"><img hspace="5" height="98" width="98" border="0" src="http://www.popsci.com/files/imagecache/photogallery_thumbnails_for_block/files/articles//{$article[3][img]}" style="border: 1px solid rgb(0, 104, 165); padding: 0px; float: left;" spname="http:__www.popsci.com_files_imagecache_photogallery_thumbnails_for_block_files_articles_{$article[3][img]}" name="http:__www.popsci.com_files_imagecache_photogallery_thumbnails_for_block_files_articles_{$article[3][img]}" alt="What's Hot 3 Image" title="What's Hot 3 Image" /></a><span style="font-weight: bold; font-size: 11px; text-transform: uppercase; color: rgb(237, 28, 36); line-height: 20px;"> {$article[3][cat]} <br />
                    </span><a name="Whats Hot 3 Title" href="{$article[3][url]}$tag" style="font-weight: bold; font-size: 13px; color: rgb(0, 104, 165); text-decoration: none;" xtlinkname="Whats Hot 1 Title" xt="SPCLICK" target="_blank">{$article[3][hed]} <br />
                    </a><!--dek-->{$article[3][dek]} <br />
                    <span style="font-weight: bold; font-size: 11px; text-transform: uppercase;">[<a name="Whats Hot 3 Read Full Story" href="{$article[3][url]}$tag" style="color: rgb(0, 104, 165); line-height: 25px; text-decoration: none;" xtlinkname="Whats Hot 3 Read Full Story" xt="SPCLICK" target="_blank">Read full story</a>]</span></p>

 <p style="border-bottom: 1px solid rgb(222, 222, 222); margin: 0px; padding: 20px 0px;"><a name="Whats Hot 4 Image" href="{$article[4][url]}$tag" xtlinkname="Whats Hot 4 Image" xt="SPCLICK" target="_blank"><img hspace="5" height="98" width="98" border="0" src="http://www.popsci.com/files/imagecache/photogallery_thumbnails_for_block/files/articles//{$article[4][img]}" style="border: 1px solid rgb(0, 104, 165); padding: 0px; float: left;" spname="http:__www.popsci.com_files_imagecache_photogallery_thumbnails_for_block_files_articles_{$article[4][img]}" name="http:__www.popsci.com_files_imagecache_photogallery_thumbnails_for_block_files_articles_{$article[4][img]}" alt="What's Hot 4 Image" title="What's Hot 4 Image" /></a><span style="font-weight: bold; font-size: 11px; text-transform: uppercase; color: rgb(237, 28, 36); line-height: 20px;"> {$article[4][cat]} <br />
                    </span><a name="Whats Hot 4 Title" href="{$article[4][url]}$tag" style="font-weight: bold; font-size: 13px; color: rgb(0, 104, 165); text-decoration: none;" xtlinkname="Whats Hot 4 Title" xt="SPCLICK" target="_blank">{$article[4][hed]} <br />
											</a><!--dek-->{$article[4][dek]} <br />
                    <span style="font-weight: bold; font-size: 11px; text-transform: uppercase;">[<a name="Whats Hot 4 Read Full Story" href="{$article[4][url]}$tag" style="color: rgb(0, 104, 165); line-height: 25px; text-decoration: none;" xtlinkname="Whats Hot 4 Read Full Story" xt="SPCLICK" target="_blank">Read full story</a>]</span></p>





</td>
                </tr>
                <tr>
                    <td valign="bottom" width="412" align="left" style="border-style: solid; border-color: rgb(222, 222, 222); border-width: 0px 0px 10px 10px; padding: 10px; width: 368px;">
                    
<a href="http://ad.doubleclick.net/jump/n6747.popsci/newsletter;pos=x61;sz=300x250;topic=technology;tile=2;ord=23456781?" target="_blank">

<img src="http://ad.doubleclick.net/ad/n6747.popsci/newsletter;pos=x61;sz=300x250;topic=technology;tile=2;ord=23456781?" width="300" height="250" border="0" alt="">

</a>


                    </td>
                    <td valign="bottom" width="378" align="right" style="border-style: solid; border-color: rgb(222, 222, 222); border-width: 0px 10px 10px 0px; padding: 10px; width: 300px;">
                    <table width="348" cellspacing="0" cellpadding="0" border="0">
                        <tbody>
                            <tr>
                                <td valign="bottom" width="375" align="right">
                                
<a href="http://ad.doubleclick.net/jump/n6747.popsci/newsletter;pos=x62;sz=300x250;topic=technology;tile=3;ord=23456781?" target="_blank">

<img src="http://ad.doubleclick.net/ad/n6747.popsci/newsletter;pos=x62;sz=300x250;topic=technology;tile=3;ord=23456781?" width="300" height="250" border="0" alt="">

</a>

                                </td>
                            </tr>
                        </tbody>
                    </table>
                    </td>
                </tr>
            </tbody>
        </table>

        
        <table cellspacing="3" cellpadding="3" border="0" align="center" width="650">
            <tbody>
            <tr>
                    <td height="2" align="center" valign="top" colspan="5"><hr size="1" noshade="" color="#cccccc" align="center" width="95%" />
                    </td>
                </tr>
                <tr>
                    <td align="left" width="122" valign="middle"> </td>
                    <td align="left" width="156" valign="middle">
                    <div style="margin: 10px 5px; float: none; vertical-align: middle; width: 140px;">
                    <div align="center"><font face="Arial, Helvetica, sans-serif" style="font-weight: normal; font-size: 11px; color: rgb(0, 0, 0); line-height: 20px;">More from <b>PopSci.com</b>: </font></div>
                    </div>
                    </td>
                    <td align="center" width="354" valign="bottom">
                    <div style="margin: 10px 5px; float: none; vertical-align: middle;"><font face="Arial, Helvetica, sans-serif" style="font-weight: normal; font-size: 11px; color: rgb(0, 0, 0); line-height: 20px;"><a target="_blank" xt="SPCLICK" xtlinkname="Facebook" href="http://www.facebook.com/pages/Popular-Science/60342206410?ref=ts" name="Facebook"><img height="36" border="0" width="100" title="Become a fan of PopSci on Facebook" alt="Become a fan of PopSci on Facebook" name="Facebook4.jpg" xt="SPIMAGE" spname="Facebook4.jpg" contentid="53c0d-12b826b9846-1973771dea71da7e4c551ed9f05528be" src="Facebook4.jpg" /></a></font></div>
                    </td>
                    <td align="center" width="353" valign="bottom">
                    <div style="margin: 10px 5px; float: none; vertical-align: middle;"><font face="Arial, Helvetica, sans-serif" style="font-weight: normal; font-size: 11px; color: rgb(0, 0, 0); line-height: 20px;"><a target="_blank" xt="SPCLICK" xtlinkname="Twitter" href="http://twitter.com/popsci" name="Twitter"><img height="34" border="0" width="100" title="Follow PopSci on Twitter" alt="Follow PopSci on Twitter" name="Twitter4.jpg" xt="SPIMAGE" spname="Twitter4.jpg" contentid="53c0d-12b826b9742-1973771dea71da7e4c551ed9f05528be" src="Twitter4.jpg" /></a></font></div>
                    </td>
                    <td align="left" width="128" valign="middle"> </td>
                </tr>
                <tr>
                    <td height="2" align="center" valign="top" colspan="5"><hr size="1" noshade="" color="#cccccc" align="center" width="95%" />
                    </td>
                </tr>
                <tr>
                   
                    <td align="center" colspan="5"><font face="Arial, Helvetica, sans-serif" style="font-weight: normal; font-size: 11px; color: rgb(0, 0, 0);"><a target="_blank" xt="SPPROFILE" xtlinkname="Profile" style="color: rgb(0, 0, 0); text-decoration: underline;" href="#SPPROFILE" name="Profile">Change your preferences</a> | <a target="_blank" xt="SPOPTOUT" xtlinkname="Unsubscribe" style="color: rgb(0, 0, 0); text-decoration: underline;" href="#SPOPTOUT" name="Unsubscribe">Unsubscribe</a> | <a target="_blank" xt="SPCLICK" xtlinkname="Privacy Policy" style="color: rgb(0, 0, 0); text-decoration: underline;" href="http://bonniercorp.com/privacy_policy.html" name="Privacy Policy">Privacy Policy</a> | <a target="_blank" xt="SPFORWARD" xtlinkname="Forward" style="color: rgb(0, 0, 0); text-decoration: underline;" href="#SPFORWARD" name="Forward">Forward this email</a> </font></td>
                </tr>
                <tr>
                    <td align="center" colspan="5"><font face="Arial, Helvetica, sans-serif" style="font-weight: normal; font-size: 11px; color: rgb(0, 0, 0); line-height: 20px;">Add <a target="_blank" xt="SPEMAIL" xtlinkname="Email" style="color: rgb(0, 0, 0); text-decoration: underline;" href="mailto:newsletter@email.popsci.com" name="Email">newsletter@email.popsci.com</a> to your address book to ensure our emails reach your inbox.</font></td>
                </tr>
                <tr>
                    <td align="center" colspan="5"><font face="Arial, Helvetica, sans-serif" style="font-weight: normal; font-size: 11px; color: rgb(0, 0, 0);">Copyright &copy; <b>Bonnier Corporation</b>, 460 N. Orlando Ave., Suite 200, Winter Park, FL 32789</font></td>
                </tr>
                <tr>
                    <td height="2" align="center" valign="top" colspan="5"><hr size="1" noshade="" color="#cccccc" align="center" width="95%" />
                    </td>
                </tr>
            </tbody>
        </table>

        </center>
    </body>
</html>



EONL;


$textnewsletter = <<<EONL
$snippet
----------------------------------------

View this email online
http://recp.rm05.net/servlet/MailView?m=%%MAILING_ID%%&r=%%RECIPIENT_ID%%&j=%%JOB_ID_CODE%%&mt=1

----------------------------------------

POPSCI.COM NEWSLETTER
The Future Now
www.popsci.com

----------------------------------------
{$article[0][cat]}
{$article[0][hed]}
{$article[0][dek]}

- Read Full Story:
{$article[0][url]}$tag
------------------------------------------------------------

What's Hot this Week

{$article[1][cat]}
{$article[1][hed]}
{$article[1][dek]}

- Read Full Story:
{$article[1][url]}$tag

{$article[2][cat]}
{$article[2][hed]}
{$article[2][dek]}

- Read Full Story:
{$article[2][url]}$tag

{$article[3][cat]}
{$article[3][hed]}
{$article[3][dek]}

- Read Full Story:
{$article[3][url]}$tag

{$article[4][cat]}
{$article[4][hed]}
{$article[4][dek]}

- Read Full Story:
{$article[4][url]}$tag

------------------------------------------------------------

This Week's Top Photo Galleries

- {$gallery[0][hed]}
{$gallery[0][dek]}
{$gallery[0][url]}$tag
- {$gallery[1][hed]}
{$gallery[1][dek]}
{$gallery[1][url]}$tag
- {$gallery[2][hed]}
{$gallery[2][dek]}
{$gallery[2][url]}$tag

- More Photo Galleries
http://www.popsci.com/galleries/?cmpid=PSCenews

----------------------------------------

Subscribe to Popular Science
http://www.popsci.com/enews-circ-offer

----------------------------------------

Change your preferences
http://recp.rm02.net/ui/modules/display/previewProfile.jsp

----------------------------------------

Unsubscribe
%%HYPERLINK:Opt Out#SPCUSTOMOPTOUT#SPCUSTOMOPTOUT#%%

----------------------------------------

Privacy Policy
http://bonniercorp.com/privacy_policy.html

----------------------------------------

Forward this email
http://recp.rm02.net/ui/modules/display/previewFM.jsp

----------------------------------------

Add newsletter@email.popsci.com to your address book to ensure our emails reach your inbox.

----------------------------------------

Subscribe: 

Popular Science
http://www.popsci.com/enews-circ-offer

Science Illustrated
https://secure.palmcoastd.com/pcd/eSv?iMagId=08946&i4Ky=HANE

----------------------------------------

Copyright (C) Bonnier Corporation, 460 N. Orlando Ave., Suite 200, Winter Park, FL 32789                                          


EONL;

echo $htmlnewsletter;

echo "<hr><p>HTML NEWSLETTER<br><textarea>$htmlnewsletter</textarea>";
echo "<p>TEXT NEWSLETTER<br><textarea>$textnewsletter</textarea>";



?>
