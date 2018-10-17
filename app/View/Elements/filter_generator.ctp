

<script type="text/javascript">
    var countries = JSON.parse('<?php echo json_encode($countries, JSON_HEX_APOS); ?>');
    var cities = JSON.parse('<?php echo json_encode($cities, JSON_HEX_APOS); ?>');
    var institutions = JSON.parse('<?php echo addslashes(json_encode($institutions, JSON_HEX_APOS)); ?>');
    var education_types = JSON.parse('<?php echo json_encode($course_types, JSON_HEX_APOS); ?>');
    var disciplines = JSON.parse('<?php echo json_encode($disciplines, JSON_HEX_APOS); ?>');
    var objects = JSON.parse('<?php echo json_encode($objects, JSON_HEX_APOS); ?>');
    var techniques = JSON.parse('<?php echo json_encode($techniques, JSON_HEX_APOS); ?>');

    var generatorOutput, generatorUrl, generatorResult, generatorFilterField, generatorFilterValue

    function initGenerator() {
        $('#generator-filter-field,#generator-filter-value,input[name="data[output]"]').change(function () {
            createResult()
        })
        generatorFilterField = $('#generator-filter-field').val()
        generatorResult = $('#generator-result')
        createResult()
    }

    function createResult() {
        readGeneratorForm()
        createUrl()
        console.log(generatorOutput)
        switch (generatorOutput) {
            case 'link':
            case 'xml':
            case 'json':
                generatorResult.val(generatorUrl)
                break
            case 'iframe':
                var result = '<iframe\nsrc="' + generatorUrl + '"\nwidth="100%" id="dhcr-iframe"/>'
                generatorResult.val(result)
        }
    }

    function readGeneratorForm() {
        generatorOutput = $('input[name="data[output]"]:checked').val()
        var newField = $('#generator-filter-field').val()
        if(generatorFilterField != newField) {
            generatorFilterField = newField
            if(generatorFilterField != 0) {
                $('#generator-filter-value-div').show()
            }else{
                $('#generator-filter-value-div').hide()
            }
            // swap options of the filter value selector
            var $el = $('#generator-filter-value')
            // remove old options
            $el.empty()
            switch(generatorFilterField) {
                case 'country_id':
                    $.each(countries, function(key,value) {
                        $el.append($("<option></option>")
                            .attr("value", key).text(value))
                    })
                    break
                case 'city_id':
                    $.each(cities, function(key,value) {
                        var optgroup = $("<optgroup></optgroup>")
                        $el.append(optgroup.attr("label", key).append(
                                $.each(value, function(k,v) {
                                    optgroup.append($("<option></option>")
                                        .attr("value", k).text(v))
                                })
                            ))
                    })
                    break
                case 'institution_id':
                    $.each(institutions, function(key,value) {
                        var optgroup = $("<optgroup></optgroup>")
                        $el.append(optgroup.attr("label", key).append(
                            $.each(value, function(k,v) {
                                optgroup.append($("<option></option>")
                                    .attr("value", k).text(v))
                            })
                        ))
                    })
                    break
                case 'course_type_id':
                    $.each(education_types, function(key,value) {
                        $el.append($("<option></option>")
                            .attr("value", key).text(value))
                    })
                    break
                case 'discipline_id':
                    $.each(disciplines, function(key,value) {
                        $el.append($("<option></option>")
                            .attr("value", key).text(value))
                    })
                    break
                case 'tadirah_object_id':
                    $.each(objects, function(key,value) {
                        $el.append($("<option></option>")
                            .attr("value", key).text(value))
                    })
                    break
                case 'tadirah_technique_id':
                    $.each(techniques, function(key,value) {
                        $el.append($("<option></option>")
                            .attr("value", key).text(value))
                    })
                    break
            }
        }
        generatorFilterValue = $('#generator-filter-value').val()
    }

    function createUrl() {
        generatorUrl = '<?php echo Router::url("/", true); ?>'
        if(generatorOutput == 'iframe') generatorUrl += 'iframe/'

        if(generatorFilterField != 0 || generatorOutput == 'xml' || generatorOutput == 'json')
            generatorUrl += 'courses/index'

        if(generatorFilterField != 0)
            generatorUrl += '/' + generatorFilterField + ':' + generatorFilterValue

        if(generatorOutput == 'xml') generatorUrl += '.xml'
        if(generatorOutput == 'json') generatorUrl += '.json'
    }


</script>

<?php
// we need to init that functionality after jQuery has been loaded, thus add to script block at end of document
$this->Html->scriptBlock(
    'initGenerator()',
    array('inline' => false)
);
?>

<form id="generator">
    <?php
    $options = array(
        0 => '- none -',
        'country_id' => 'countries',
        'city_id' => 'cities',
        'institution_id' => 'institutions',
        'course_type_id' => 'education types',
        'discipline_id' => 'disciplines',
        'tadirah_object_id' => 'objects',
        'tadirah_technique_id' => 'techniques');

    echo $this->Form->input('filter', array(
        'label' => 'Filter Attribute',
        'id' => 'generator-filter-field',
        'type' => 'select',
        'selected' => 'country_id',
        'options' => $options
    ));
    echo $this->Form->input('value', array(
        'label' => 'Filter Value',
        'id' => 'generator-filter-value',
        'type' => 'select',
        'options' => $countries,
        'div' => array('id' => 'generator-filter-value-div')
    ));
    echo $this->Form->input('output', array(
        'label' => 'Output Format',
        'type' => 'radio',
        'value' => 'link',
        'options' => array(
            'link'=>'link','iframe'=>'iframe','json'=>'json','xml'=>'xml')
    ));
    echo $this->Form->input('result', array(
        'label' => 'Result',
        'id' => 'generator-result',
        'type' => 'textarea'
    ));
    ?>
</form>

