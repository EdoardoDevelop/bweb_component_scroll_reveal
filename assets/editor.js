( function( blocks, element, components) {
    var itemclass = [];
    itemclass.push({
        "value": '',
        "label": '---'
    });
    bc_reveal_class.forEach(element => {
        itemclass.push({
            "value": element.class.replace(".", ""),
            "label": element.class.replace(".", "")
        })
        
    });

    function addRevealAttribute(settings, name) {
        if (typeof settings.attributes !== 'undefined') {
            if (name == 'core/group' || name == 'core/column') {
                settings.attributes = Object.assign(settings.attributes, {
                    revealclass: {
                        type: 'string',
                    }
                });
            }
        }
        return settings;
    }
    
    wp.hooks.addFilter(
        'blocks.registerBlockType',
        'bc/reveal-custom-attribute',
        addRevealAttribute
    );


    const RevealAdvancedControls = wp.compose.createHigherOrderComponent((BlockEdit) => {
        return (props) => {
            var el = element.createElement;
            const { Fragment } = wp.element;
            const { SelectControl } = wp.components;
            const { InspectorAdvancedControls } = wp.blockEditor;
            const { attributes, setAttributes, isSelected } = props;
            return el(Fragment, {
                children: [el(BlockEdit, {
                    ...props
                }), isSelected && (props.name == 'core/group' || props.name == 'core/column') && el(InspectorAdvancedControls, {
                    children: el("div", {},
                    el(SelectControl, {
                        label: "Animazione",
                        value: attributes.revealclass,
                        options: itemclass,
                        onChange: ( newval ) => {
                            setAttributes({
                                revealclass: newval
                            });
                            ScrollReveal().destroy();
                            bc_reveal_class.forEach(element => {
                                if(element.class.replace(".", "") === newval){
                                    ScrollReveal().reveal('#reveal_preview',{
                                        distance: element.distance,
                                        origin: element.origin,
                                        duration: element.duration,
                                        easing: element.easing,
                                        interval: element.interval,
                                    })
                                }
                                
                            });
                            
                        }
                    }),
                    el("div", {id:'reveal_preview'}, 'Anteprima animazione')
                    )
                })]
            });
        };
    }, 'RevealAdvancedControls');
    
    wp.hooks.addFilter(
        'editor.BlockEdit',
        'bc/reveal-advanced-control',
        RevealAdvancedControls
    );

    function RevealApplyExtraClass(extraProps, blockType, attributes) {
        const { revealclass } = attributes;
        if (blockType.name == 'core/group' || blockType.name == 'core/column') {
            if (typeof revealclass !== 'undefined') {
                extraProps.className = extraProps.className + ' ' + revealclass;
            }
        }
        return extraProps;
    }
    
    wp.hooks.addFilter(
        'blocks.getSaveContent.extraProps',
        'bc/reveal-apply-class',
        RevealApplyExtraClass
    );
} )( window.wp.blocks, window.wp.element, window.wp.components );