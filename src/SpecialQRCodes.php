<?php

namespace MediaWiki\Extension\QRLite;

use FormSpecialPage;
use HTMLForm;

class SpecialQRCodes extends FormSpecialPage {

	/** @var string */
	private string $qrCodesHtml = '';

	/**
	 * @inheritDoc
	 */
	public function __construct(
		$name = 'QRCodes', $restriction = '', $listed = true,
		$function = false, $file = '', $includable = true
	) {
		parent::__construct( $name, $restriction, $listed, $function, $file, $includable );
	}

	/**
	 * @inheritDoc
	 */
	protected function getDisplayFormat() {
		return 'ooui';
	}

	/**
	 * @inheritDoc
	 */
	protected function getMessagePrefix() {
		return 'qrlite';
	}

	/**
	 * @inheritDoc
	 */
	protected function alterForm( HTMLForm $form ) {
		// @todo After dropping support for MW < 1.40, use $this->requiresPost() instead.
		$form->setMethod( 'get' );
	}

	/**
	 * @inheritDoc
	 */
	public function execute( $par ) {
		$this->addHelpLink( 'Help:Extension:QRLite' );
		$this->checkPermissions();
		parent::execute( $par );

		if ( $this->qrCodesHtml ) {
			$this->getForm()->showAlways();
			$this->getOutput()->addHTML( $this->qrCodesHtml );
		}
	}

	/**
	 * @inheritDoc
	 */
	protected function getFormFields() {
		return [
			'url' => [
				'required' => true,
				'type' => 'url',
				'name' => 'url',
				'label-message' => 'qrlite-specialpage-url-label',
				'autofocus' => true,
				'help' => $this->msg( 'qrlite-specialpage-url-help' )->text(),
			],
			'size' => [
				'type' => 'int',
				'name' => 'size',
				'default' => 6,
				'label-message' => 'qrlite-specialpage-size-label',
				'help' => $this->msg( 'qrlite-specialpage-size-help' )->text(),
			],
		];
	}

	/**
	 * @inheritDoc
	 */
	public function onSubmit( array $data ) {
		$params = [
			'prefix' => $data['url'],
			// 'format' => $data['format'] ?? null,
			'size' => $data['size'] ?? null,
			// 'margin' => $data['margin'] ?? null,
			// 'ecc' => $data['ecc'] ?? null,
		];
		$this->qrCodesHtml = QRLiteFunctions::generateQRCode( array_filter( $params ) );
		return true;
	}

}
