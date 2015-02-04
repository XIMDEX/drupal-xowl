###
@author Ximdex <dev@ximdex.com>
###
window.ceditor = null 
selectedTextAnnotation = null 
currentSelectedSuggestion = null 
suggestions_field = jQuery '#suggestions_field' 
(($) ->
    CKEDITOR.config.allowedContent = true
    if  xowlSettings != undefined
        icon = "#{xowlSettings.basedir}/js/plugins/xowl/icons/xowl_enhance_plugin_button.png"
    else 
        icon = 'abbr'

    CKEDITOR.plugins.add 'xowl', 
        icons: icon
        init: (editor) ->
            window.parent.editor = editor 
            if  xowlSettings is undefined
                return
            if  xowlSettings.enabled == undefined or  xowlSettings.enabled != 1 
                return 
            if  CKEDITOR.xowl is undefined 
                CKEDITOR.xowl = 
                    entities: {}
                    suggestions: {}
            editor.ui.addButton 'xowl', 
                label: 'Xowl Enhance' 
                icon:  icon  
                command: 'xowl_content_enhance_command'
            #
            editor.addCommand  'xowl_content_enhance_command',
                exec: () ->
                    content = editor.getData() 
                    content = prepareContent  content  
                    $loader = $ "<div/>", 
                        class: 'loader' 
                    $("<img/>")
                    .attr 'src', "#{xowlSettings.basedir}/js/plugins/xowl/icons/loader.gif"
                    .appendTo $loader 
                    #
                    $.ajax
                        type: 'POST' 
                        dataType: "json" 
                        url: xowlSettings.url  
                        async: false,
                        data: 
                            content: content
                    .done (data) ->
                        $loader.remove()
                        if (data) 
                            result = data
                            CKEDITOR.xowl['lastResponse'] = result
                            editor.setData '', (arg) ->
                                this.insertHtml replaceXowlAnnotations result 
                                fillSuggestionsField()
                                return
                            return
                    .fail (jqXHR, textStatus, errorThrown)->
                        $loader.remove()
                        alert "Error retrieving content from XOwl" 
                        return
                    return
                #
                CKEDITOR.dialog.add 'xowl_dialog',  ( api )->
                    dialogDefinition = 
                        title: 'Select Entity Dialog'
                        minWidth: 390
                        minHeight: 130
                        contents: [
                                id: 'tab_entities',
                                label: 'Select Entity',
                                title: 'Select Entity',
                                expand: true,
                                padding: 0,
                                elements: [
                                        type: 'select',
                                        id: 'xowl_entities'
                                        label: 'Select Entities'
                                        # style: 'border: 2px solid #c00;'
                                        items: [ ]
                                        onChange: (e) ->
                                            CKEDITOR.xowl.suggestions[selectedTextAnnotation] = this.getValue()
                                            return
                                        ]
                                ]
                        buttons: [
                            CKEDITOR.dialog.okButton ,
                            CKEDITOR.dialog.cancelButton ,
                                type: 'button'
                                id: 'removeSuggestion'
                                label: 'Remove'
                                title: 'Remove'
                                onClick: ()->
                                    removeSuggestion editor
                                    this
                                    .getDialog()
                                    .hide()
                                    return
                            ]
                        onOk:  () ->
                            selectedEntityUri = CKEDITOR.xowl.suggestions[ selectedTextAnnotation ]
                            selectedEntityType = CKEDITOR.xowl.tempTypes[ selectedEntityUri ]
                            $ this.getParentEditor().window.getFrame().$ 
                            .contents()
                            .find "[data-cke-annotation=\"#{selectedTextAnnotation}\"]" 
                            .attr
                                "href": CKEDITOR.xowl.suggestions[ selectedTextAnnotation ]
                                "data-cke-saved-href" : CKEDITOR.xowl.suggestions[ selectedTextAnnotation ]
                                "data-cke-type": selectedEntityType
                            $(this.getParentEditor().window.getFrame().$)
                            .contents()
                            .find "[data-cke-annotation=\"#{selectedTextAnnotation}\"]" 
                            .removeAttr "data-cke-suggestions" 
                            fillSuggestionsField()
                            return
                        onCancel: () ->
                            CKEDITOR.xowl.suggestions[ selectedTextAnnotation ] = currentSelectedSuggestion 
                            return
                        onShow: () ->
                            dialogTabSelect = this.getContentElement 'tab_entities', 'xowl_entities'  
                            entities = CKEDITOR.xowl.entities[ selectedTextAnnotation ]
                            CKEDITOR.xowl.tempTypes = {}
                            dialogTabSelect.clear()
                            entities.forEach  (entity) ->
                                dialogTabSelect.add "#{entity.label} (#{entity.uri})", entity.uri 
                                CKEDITOR.xowl.tempTypes[ entity.uri ] = entity.type 
                                return
                            dialogTabSelect.setValue CKEDITOR.xowl.suggestions[ selectedTextAnnotation ] 
                            currentSelectedSuggestion = CKEDITOR.xowl.suggestions[ selectedTextAnnotation ] 
                            return
                    dialogDefinition
                    #
                editor.on 'contentDom', (e) ->
                    $ editor.document.$ 
                    .unbind 'keyup' 
                    .bind 'keyup', ( evt ) ->
                        if evt.keyCode == 8 || evt.keyCode == 46 
                            evt.stopPropagation() 
                            $ editor.document.$ 
                            .find "[data-cke-annotation]"
                            .each  (i,element) ->
                                $el = $(element)
                                if $el.html() != $el.attr("data-cke-annotation") 
                                    delete CKEDITOR.xowl.suggestions[ $el.attr("data-cke-annotation") ] 
                                    $el.replaceWith $el.html() 
                                return 
                            fillSuggestionsField()
                        return 

                editor.on 'change',  (e) ->
                    $ e.editor.window.getFrame().$ 
                    .contents()
                    .find '.xowl-suggestion' 
                    .unbind 'click'
                    .bind  'click' ,  () ->
                        selectedTextAnnotation = $(this).data 'cke-annotation' 
                        # selectedTextAnnotation = $(this).html()
                        openXowlDialog e.editor 
                        return
                    return
                

            

            return
    #
    #  functions
    # 
    ###
    <p>Cleans the content, removing the links put by the plugin</p>
    <p>Adds some marks to let Xowl enhance html content successfully</p>
    @param content String The content to be prepared
    @returns String The prepared content
    ###
    prepareContent = ( content) ->
        content
    ### 
    <p>Function to replace text annotation mentions by the entity annotation URI</p>
    @param result object result Containing the text, Text Annotations (with positions) and Entity Annotations
    @returns The text with the found mentions replaced
    ### 
    replaceXowlAnnotations =(result) ->
        text = result.text
        CKEDITOR.xowl.suggestions = {}
        CKEDITOR.xowl.entities = {}
        if  result.semantic 
            text = processSemanticAnnotations text, result.semantic 
        text
    removeSuggestion = (editor)->
        $(editor.window.getFrame().$)
        .contents()
        .find "[data-cke-annotation=\"#{selectedTextAnnotation}\"]" 
        .replaceWith selectedTextAnnotation 
        delete CKEDITOR.xowl.suggestions[ selectedTextAnnotation ] 
        return
    openXowlDialog  = (editor) ->
        if  !CKEDITOR.xowl || !CKEDITOR.xowl.entities
            return
        editor.openDialog 'xowl_dialog' 
        return
    fillSuggestionsField = ()->
        suggestions_field.val JSON.stringify CKEDITOR.xowl.suggestions 
        return
    # 
    # 
    processSemanticAnnotations = (text, annotations) ->
        for textAnnotation in annotations
            mention = textAnnotation['selected-text']
            entity = textAnnotation.entities[0]
            numSuggestions = textAnnotation.entities.length
            CKEDITOR.xowl.suggestions[mention] = entity.uri
            CKEDITOR.xowl.entities[mention] = textAnnotation.entities
        text
    #
    return 

) jQuery

