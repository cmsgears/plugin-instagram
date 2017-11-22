<?php
namespace cmsgears\instagram;

//Yii Imports
use yii\helpers\Html;

class InstagramPosts extends  \cmsgears\core\common\base\Widget {

	public $limit	= 5;
	public $header	= null;
	public $accessToken;

	public function init() {

		parent::init();
	}

	public function renderWidget( $config = [] ) {

		$instaPosts		= [];
		$postsHtml		= [];
		$singlePath		= "$this->template/single";
		$wrapperView	= "$this->template/wrapper";

		// Instagram Specific Configuration
		$jsonLink			= "https://api.instagram.com/v1/users/self/media/recent/?";
		$jsonLink			.= "access_token={$this->accessToken}&count={$this->limit}";
		$json				= @file_get_contents( $jsonLink);
		$decoded_results	= json_decode( preg_replace( '/("\w+"):(\d+)/', '\\1:"\\2"', $json ), true );

		if( $json !== false ) {

			foreach( $decoded_results[ 'data' ] as $item ) {

				$image_link		= $item[ 'images' ][ 'thumbnail' ][ 'url' ];
				$url			= $item[ 'link' ];

				$instaPosts[]	= [ 'img' => $image_link, 'url' => $url ];
			}
		}

		foreach( $instaPosts as $post ) {

			$postsHtml[]	= $this->render( $singlePath, [ 'post' => $post, 'widget' => $this ] );
		}

		$postsHtml	= implode( '', $postsHtml);
		$postsHtml	= $this->render( $wrapperView, [ 'postsHtml' => $postsHtml, 'widget' => $this ] );

		return Html::tag( 'div', $postsHtml, $this->options );
	}
}
?>