/*
Copyright © 2003-2012, CKSource - Frederico Knabben. All rights reserved.
For licensing, see LICENSE.html or http://ckeditor.com/license
*/

CKEDITOR.editorConfig = function( config )
{
	config.width = 600;
	config.height = 600;
	config.removePlugins = 'resize';

	config.toolbar = 'MyToolbar';

	config.toolbar_MyToolbar =
	[
		{ name: 'document', items : [ 'Source' ] },
		{ name: 'clipboard', items : [ 'Cut','Copy','Paste','PasteText','PasteFromWord','-','Undo','Redo' ] },
		{ name: 'editing', items : [ 'Find','Replace','Scayt' ] },
		{ name: 'insert', items : [ 'Image','Flash','Table','HorizontalRule','Smiley','SpecialChar'] },
		{ name: 'colors', items : ['TextColor', 'BGColor'] },
                '/',
		{ name: 'basicstyles', items : [ 'Bold','Italic','Strike' ] },
		{ name: 'links', items : [ 'Link','Unlink','Anchor' ] },
		{ name: 'styles', items : [ 'Styles', 'Font', 'FontSize'] },
		{ name: 'paragraph', items : [ 'NumberedList','BulletedList','Outdent','Indent','Blockquote' ] }
		
	];
	
	config.toolbar_Full =
	[
		{ name: 'document', items : [ 'Source','-','Save','NewPage','DocProps','Preview','Print','-','Templates' ] },
		{ name: 'clipboard', items : [ 'Cut','Copy','Paste','PasteText','PasteFromWord','-','Undo','Redo' ] },
		{ name: 'editing', items : [ 'Find','Replace','-','SelectAll','-','SpellChecker', 'Scayt' ] },
		{ name: 'forms', items : [ 'Form', 'Checkbox', 'Radio', 'TextField', 'Textarea', 'Select', 'Button', 'ImageButton', 
	        'HiddenField' ] },
		'/',
		{ name: 'basicstyles', items : [ 'Bold','Italic','Underline','Strike','Subscript','Superscript','-','RemoveFormat' ] },
		{ name: 'paragraph', items : [ 'NumberedList','BulletedList','-','Outdent','Indent','-','Blockquote','CreateDiv',
		'-','JustifyLeft','JustifyCenter','JustifyRight','JustifyBlock','-','BidiLtr','BidiRtl' ] },
		{ name: 'links', items : [ 'Link','Unlink','Anchor' ] },
		{ name: 'insert', items : [ 'Image','Flash','Table','HorizontalRule','Smiley','SpecialChar','PageBreak','Iframe' ] },
		'/',
		{ name: 'styles', items : [ 'Styles','Format','Font','FontSize' ] },
		{ name: 'colors', items : [ 'TextColor','BGColor' ] },
		{ name: 'tools', items : [ 'Maximize', 'ShowBlocks','-','About' ] }
	];
	 
	config.toolbar_Basic =
	[
		['Bold', 'Italic', '-', 'NumberedList', 'BulletedList', '-', 'Link', 'Unlink','-','About']
	];


	/*
	 * Style sheet for the contents
	 */
	config.contentsCss = 'body {color:#000; background-color#:FFF;}';

	/*
	 * Simple HTML5 doctype
	 */
	config.docType = '<!DOCTYPE HTML>';

	/*
	 * Core styles.
	 */
	config.coreStyles_bold	= { element : 'b' };
	config.coreStyles_italic	= { element : 'i' };
	config.coreStyles_underline	= { element : 'u'};
	config.coreStyles_strike	= { element : 'strike' };

	/*
	 * Font face
	 */
	// Define the way font elements will be applied to the document. The "font"
	// element will be used.
	config.font_style =
	{
			element		: 'font',
			attributes		: { 'face' : '#(family)' }
	};

	/*
	 * Font sizes.
	 */
	config.fontSize_sizes = 'xx-small/1;x-small/2;small/3;medium/4;large/5;x-large/6;xx-large/7';
	config.fontSize_style =
		{
			element		: 'font',
			attributes	: { 'size' : '#(size)' }
		};

	/*
	 * Font colors.
	 */
	config.colorButton_enableMore = true;

	config.colorButton_foreStyle =
		{
			element : 'font',
			attributes : { 'color' : '#(color)' }
		};

	config.colorButton_backStyle =
		{
			element : 'font',
			styles	: { 'background-color' : '#(color)' }
		};

	/*
	 * Styles combo.
	 */
	config.stylesSet =
			[
				{ name : 'Computer Code', element : 'code' },
				{ name : 'Keyboard Phrase', element : 'kbd' },
				{ name : 'Sample Text', element : 'samp' },
				{ name : 'Variable', element : 'var' },

				{ name : 'Deleted Text', element : 'del' },
				{ name : 'Inserted Text', element : 'ins' },

				{ name : 'Cited Work', element : 'cite' },
				{ name : 'Inline Quotation', element : 'q' }
			];

	config.on = { 'instanceReady' : configureHtmlOutput };

	
};








/*
 * Adjust the behavior of the dataProcessor to avoid styles
 * and make it look like FCKeditor HTML output.
 */
function configureHtmlOutput( ev )
{
	var editor = ev.editor,
		dataProcessor = editor.dataProcessor,
		htmlFilter = dataProcessor && dataProcessor.htmlFilter;

	// Out self closing tags the HTML4 way, like <br>.
	dataProcessor.writer.selfClosingEnd = '>';

	// Make output formatting behave similar to FCKeditor
	var dtd = CKEDITOR.dtd;
	for ( var e in CKEDITOR.tools.extend( {}, dtd.$nonBodyContent, dtd.$block, dtd.$listItem, dtd.$tableContent ) )
	{
		dataProcessor.writer.setRules( e,
			{
				indent : true,
				breakBeforeOpen : true,
				breakAfterOpen : false,
				breakBeforeClose : !dtd[ e ][ '#' ],
				breakAfterClose : true
			});
	}

	// Output properties as attributes, not styles.
	htmlFilter.addRules(
		{
			elements :
			{
				$ : function( element )
				{
					// Output dimensions of images as width and height
					if ( element.name == 'img' )
					{
						var style = element.attributes.style;

						if ( style )
						{
							// Get the width from the style.
							var match = /(?:^|\s)width\s*:\s*(\d+)px/i.exec( style ),
								width = match && match[1];

							// Get the height from the style.
							match = /(?:^|\s)height\s*:\s*(\d+)px/i.exec( style );
							var height = match && match[1];

							if ( width )
							{
								element.attributes.style = element.attributes.style.replace( /(?:^|\s)width\s*:\s*(\d+)px;?/i , '' );
								element.attributes.width = width;
							}

							if ( height )
							{
								element.attributes.style = element.attributes.style.replace( /(?:^|\s)height\s*:\s*(\d+)px;?/i , '' );
								element.attributes.height = height;
							}
						}
					}

					// Output alignment of paragraphs using align
					if ( element.name == 'p' )
					{
						style = element.attributes.style;

						if ( style )
						{
							// Get the align from the style.
							match = /(?:^|\s)text-align\s*:\s*(\w*);/i.exec( style );
							var align = match && match[1];

							if ( align )
							{
								element.attributes.style = element.attributes.style.replace( /(?:^|\s)text-align\s*:\s*(\w*);?/i , '' );
								element.attributes.align = align;
							}
						}
					}

					if ( !element.attributes.style )
						delete element.attributes.style;

					return element;
				}
			},

			attributes :
				{
					style : function( value, element )
					{
						// Return #RGB for background and border colors
						return convertRGBToHex( value );
					}
				}
		} );
}


/**
* Convert a CSS rgb(R, G, B) color back to #RRGGBB format.
* @param Css style string (can include more than one color
* @return Converted css style.
*/
function convertRGBToHex( cssStyle )
{
	return cssStyle.replace( /(?:rgb\(\s*(\d+)\s*,\s*(\d+)\s*,\s*(\d+)\s*\))/gi, function( match, red, green, blue )
		{
			red = parseInt( red, 10 ).toString( 16 );
			green = parseInt( green, 10 ).toString( 16 );
			blue = parseInt( blue, 10 ).toString( 16 );
			var color = [red, green, blue] ;

			// Add padding zeros if the hex value is less than 0x10.
			for ( var i = 0 ; i < color.length ; i++ )
				color[i] = String( '0' + color[i] ).slice( -2 ) ;

			return '#' + color.join( '' ) ;
		 });
}



