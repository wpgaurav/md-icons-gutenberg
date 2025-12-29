/**
 * WordPress dependencies
 */
import { __ } from '@wordpress/i18n';
import { registerFormatType } from '@wordpress/rich-text';
import { Fragment } from '@wordpress/element';
import { Toolbar, IconButton, Popover } from '@wordpress/components';
import IconMap from './controls';

/**
 * Block constants
 */
const NAME = 'gt-md/insert-icons';

const settings = {
    title: __('Insert Icon', 'gt-md-icons'),
    tagName: 'i',
    className: null,
    edit: ({ isActive, value, onChange, activeAttributes }) => {
        return (
            <Fragment>
                <IconMap
                    name={NAME}
                    isActive={isActive}
                    value={value}
                    onChange={onChange}
                    activeAttributes={activeAttributes}
                />
            </Fragment>
        );
    }
};

// Register the format type when DOM is ready
wp.domReady(() => {
    registerFormatType(NAME, settings);
});