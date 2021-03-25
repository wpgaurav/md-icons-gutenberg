/**
 * WordPress dependencies
 */
const { __ } = wp.i18n;
const { registerFormatType } = wp.richText;
const { Fragment } = wp.element;
const { Toolbar, IconButton, Popover } = wp.components;

//import icon from './icon';

import IconMap from './controls';

/**
 * Block constants
 */
const name = 'gt-md/insert-icons';


export const icon = {
    name,
    title: __('Insert Icon', 'gt-md-icons'),
    tagName: 'i',
    className: null,
    edit( { isOpen, value, onChange, activeAttributes } ) {
        return (
            <Fragment>
                <IconMap
                    name={ name }
                    isOpen={ isOpen }
                    value={ value }
                    onChange={ onChange }
                    activeAttributes={ activeAttributes }
                />
            </Fragment>
        );
    }
};


// Register the icon button
wp.domReady(function(){
    [
        icon,
    ].forEach( ( { name, ...settings } ) => {
        if ( name ) {
            registerFormatType( name, settings );
        }
    } );   
});
