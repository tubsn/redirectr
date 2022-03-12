<?php

namespace app\models;

use chillerlan\QRCode\{QRCode, QRCodeException, QROptions};
use chillerlan\QRCode\Data\QRMatrix;
use chillerlan\QRCode\Common\EccLevel;
use app\models\QRSvgWithLogo;


class QRGenerator
{

	public function __construct() {

	}

	public function go($qrData) {

		$gzip = true;

		$options_arr = [
			// SVG logo options (see extended class below)
			'svgLogo'             => PUBLICFOLDER . 'styles/img/lr-logo.svg', // logo from: https://github.com/simple-icons/simple-icons
			'svgLogoScale'        => 0.15,
			'svgLogoCssClass'     => 'dark',
			// QROptions
			'version'             => 5,
			'outputType'          => QRCode::OUTPUT_CUSTOM,
			'outputInterface'     => QRSvgWithLogo::class,
			'imageBase64'         => false,
			// ECC level H is necessary when using logos
			'eccLevel'            => EccLevel::H,
			'addQuietzone'        => false,
			// if set to true, the light modules won't be rendered
			'imageTransparent'    => true,
			// empty the default value to remove the fill* attributes from the <path> elements
			'markupDark'          => '',
			'markupLight'         => '',
			// draw the modules as circles isntead of squares
			'drawCircularModules' => true,
			'circleRadius'        => .45,
			// connect paths
			'svgConnectPaths'     => true,
			// keep modules of thhese types as square
			'keepAsSquare'        => [
				QRMatrix::M_FINDER|QRMatrix::IS_DARK,
				QRMatrix::M_FINDER_DOT,
				QRMatrix::M_ALIGNMENT|QRMatrix::IS_DARK,
			],
			// https://developer.mozilla.org/en-US/docs/Web/SVG/Element/linearGradient
			'svgDefs'             => '
			<linearGradient id="gradient" x1="100%" y2="100%">
				<stop stop-color="#D70071" offset="0"/>
				<stop stop-color="#9C4E97" offset="0.5"/>
				<stop stop-color="#0035A9" offset="1"/>
			</linearGradient>
			<style><![CDATA[
				.dark{fill: url(#gradient);}
				.light{fill: #eaeaea;}
			]]></style>',
		];

		// augment the QROptions class
		$options = new class ($options_arr) extends QROptions{
			// path to svg logo
			protected string $svgLogo;
			// logo scale in % of QR Code size, clamped to 10%-30%
			protected float $svgLogoScale = 0.20;
			// css class for the logo (defined in $svgDefs)
			protected string $svgLogoCssClass = '';

			// check logo
			protected function set_svgLogo(string $svgLogo):void{

				if(!file_exists($svgLogo) || !is_readable($svgLogo)){
					throw new QRCodeException('invalid svg logo');
				}

				// @todo: validate svg

				$this->svgLogo = $svgLogo;
			}

			// clamp logo scale
			protected function set_svgLogoScale(float $svgLogoScale):void{
				$this->svgLogoScale = max(0.05, min(0.3, $svgLogoScale));
			}

		};

		$qrcode = (new QRCode($options))->render($qrData);

		return $qrcode;

		/*
		header('Content-type: image/svg+xml');

		if($gzip){
			header('Vary: Accept-Encoding');
			header('Content-Encoding: gzip');
			$qrcode = gzencode($qrcode, 9);
		}

		echo $qrcode;
		*/

	}



}
