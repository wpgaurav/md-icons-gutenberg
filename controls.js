/**
 * External dependencies
 */
import { map, upperFirst } from 'lodash';

/**
 * WordPress dependencies
 */
const { __ } = wp.i18n;
const { Component, Fragment } = wp.element;
const { getRectangleFromRange } = wp.dom;
const { compose, ifCondition } = wp.compose;
const { withSelect } = wp.data;
const { BlockControls} = wp.blockEditor;
const { toggleFormat, insert, create } = wp.richText;
const { Toolbar, IconButton, Popover, Panel, Button, TextControl, Tooltip } = wp.components;

let anchorRange;
let Icons = md_richtext_icon_settings.iconset;
let classPrefix = md_richtext_icon_settings.base_class;

class IconMap extends Component {
    constructor() {
        super( ...arguments );

        this.toggle = this.toggle.bind( this );

        this.state = {
            icons: Icons,
            isOpen: false,
            keyword: '',
        };
    }

    search( keyword ) {
        let filtered = [];
    
        map( Icons, ( icon )  => {
            if ( icon.toLowerCase().search(
                keyword.toLowerCase() ) !== -1 ) {
                filtered.push(icon);
            }
        } );    

        this.setState( { keyword, icons: filtered } );
    }

    toggle() {
        this.setState( ( state ) => ( {
            isOpen: ! state.isOpen,
        } ) );

        this.setState( {  keyword: '', icons: Icons } );

        const selection = window.getSelection();
        anchorRange = selection.rangeCount > 0 ? selection.getRangeAt( 0 ) : null;
        //onChange( toggleFormat( value, { type: name } ) );
    }

    render() {
        const { isOpen, icons, keyword } = this.state;
        const { name, value, onChange } = this.props;
        const anchorRect = () => {
            return getRectangleFromRange( anchorRange );
        };

        return (
            <Fragment>
                <BlockControls>
                    <Toolbar>
                        <IconButton
                            icon={ "flag" }
                            aria-haspopup="true"
                            tooltip={ __('Insert Icon', 'gt-md-icons') }
                            onClick={ this.toggle }
                        >
                        </IconButton>
                   
            
                        { isOpen && (
                            <Popover
                                className="gt-md-icons-popover"
                                position="bottom center"
                                key="icon-popover"
                                onClick={ () => {} }
                                getAnchorRect={ anchorRect }
                                expandOnMobile={ true }
                                headerTitle={ __( 'Insert Icon', 'gt-md-icons' ) }
                                onClose={ () => {
                                    onChange( toggleFormat( value, { type: name } ) );
                                } }
                            >
                                <TextControl
                                    value={ keyword }
                                    placeholder={ __( 'Search', 'gt-md-icons' ) }
                                    onChange={ ( newKeyword ) => {
                                        this.search( newKeyword );
                                    } }
                                />
                                <div
                                    className="gt-md-icons-panel"
                                >
                                   
                                    { icons.length > 0 ? (
                                        <ul className="gt-md-icons-list">
                                            { map( icons, ( icon ) => {
                                                return (
                                                    <li data-key={ icon }>
                                                        <Tooltip text={icon}>
                                                            <Button
                                                                isTertiary
                                                                onClick={ () => {
                                                                    let temp = create({'html' : '<i class="'+classPrefix+' '+icon+'" aria-hidden="true"> </i>'});

                                                                    onChange( insert( value,  temp ) );
                                                                    
                                                                    this.toggle();
                                                                } }
                                                            >
                                                                <i className={ classPrefix + ' ' + icon } aria-hidden="true"></i>
                                                            </Button>
                                                        </Tooltip>
                                                    </li>
                                                );
                                            } ) }
                                        </ul>
                                    ) : (
                                        <p>{ __( 'No characters found.', 'block-options' ) }</p>
                                    ) }
                                </div>
                            </Popover>
                        ) }

                        </Toolbar>
                    </BlockControls>
                </Fragment>
        );
    }
}

export default compose(
    withSelect( ( select ) => {
        return {
            isDisabled: select( 'core/edit-post' ).isFeatureActive(
                'disableMDIconMap'
            ),
        };
    } ),
    ifCondition( ( props ) => {
        return ! props.isDisabled;
    } )
)( IconMap );
