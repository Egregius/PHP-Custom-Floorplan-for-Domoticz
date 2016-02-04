<?php

// Array to Chart Function
// Copyright (c) 2014, Ink Plant
// https://inkplant.com/code/array-to-chart

function array_to_chart($data,$args=false) {
    if (!is_array($args)) { $args = array(); }
    $fields = array('axis_color','background_color','bar_group_width','border_color','border_width','capitalize_headers','category_filter','chart','chart_div','colors','custom_headers','div_style','first_column_numeric','format_strings','height','hide_legend','legend_position','margins','region','resolution','responsive','smooth','style_column','text_style','title','tooltips_column','tooltips_html','width');
    $axis_fields = array('baseline','color','gridlines','max','min','text_position','text_style','title');
    foreach ($axis_fields as $key) {
        $fields[] = 'x_axis_'.$key;
        $fields[] = 'y_axis_'.$key;
    }
    foreach ($fields as $key) {
        if (array_key_exists($key,$args)) { $$key = $args[$key]; } else { $$key = false; }
    }
    if (!$chart) { $chart = 'LineChart'; }
    if (!$width) { $width = 1000; } //pixels
    if (!$height) { $height = 600; } //pixels
    if (!$chart_div) { $chart_div = 'chart_div'; }

    foreach (array('text_style','x_axis_text_style','y_axis_text_style') as $key) {
        if (!is_array($$key)) {
            if ($$key) { custom_die('Chart function requires '.$key.' to be an array.'); }
            $$key = array();
        }
    }


    //more might be added to this later, but for now, categoryFilter is the only type of dashboard-requiring control that this function supports
    if ($category_filter) { $dashboard = true; } else { $dashboard = false; }

    $eol = "\n";

    //get rid of headers row, if it exists (headers should exist as keys)
    if (array_key_exists('headers',$data)) { unset($data['headers']); }

    //the JavaScript starts here
    if ((!array_key_exists('jsapi_called',$GLOBALS)) || (!$GLOBALS['jsapi_called'])) {    
        $script = '<script type="text/javascript" src="https://www.google.com/jsapi"></script>'.$eol;
    } else {
        $script = ''; //we only need to call it once
    }
    $script .= '<script type="text/javascript">'.$eol;
    
    //if repsonsive is set to true, make the width/height dependent on screen size
    if ($responsive) {
        $script .= ' if (screen.width < '.$width.') { window.'.$chart_div.'_width = screen.width; } else { window.'.$chart_div.'_width = '.$width.'};'.$eol;
        $script .= ' window.'.$chart_div.'_height = Math.round((window.'.$chart_div.'_width / '.$width.') * '.$height.');'.$eol;
    }

    if ($dashboard) { $script .= 'google.load(\'visualization\', \'1.0\', {\'packages\':[\'controls\']});'.$eol; }
    else { $script .= 'google.load("visualization", "1", {packages:["corechart"]});'.$eol; }
    $script .= 'google.setOnLoadCallback(drawChart);'.$eol;
    $script .= 'function drawChart() {'.$eol;

    if ($responsive) {
        $script .= ' var chart_width = window.'.$chart_div.'_width;'.$eol;
        $script .= ' var chart_height = window.'.$chart_div.'_height;'.$eol;
    }

    $script .= ' var data = new google.visualization.DataTable();'.$eol;

    //SET UP COLUMNS
    $column_numbers = array(); //we use this later, for formatting
    foreach ($data as $row) {
        $j = 0;
        foreach ($row as $key => $value) {
            $column_numbers[$key] = $j;
            $j++;
            if (is_array($custom_headers) && array_key_exists($key,$custom_headers) && ($custom_headers[$key])) { $header = $custom_headers[$key]; }
            elseif ($capitalize_headers) { $header = ucwords($key); }
            else { $header = $key; }
            $label = '\''.addslashes($header).'\'';
            if ($j == 1) {
                if ($first_column_numeric) { $type = '\'number\''; }
                else { $type = '\'string\''; }
            } elseif ($j == $tooltips_column) {
                $type = '{type:\'string\',role:\'tooltip\'';
                if ($tooltips_html) { $type .= ', p:{html:true}'; }
                $type .= '}';
            } elseif ($j == $style_column) {
                $type = '{type:\'string\',role:\'style\'}';
            } else { $type = '\'number\''; }
            $script .= ' data.addColumn('.$type.','.$label.');'.$eol;
        }
        break;
    }
    
    $script .= ' data.addRows(['.$eol;

    //SET UP DATA
    $i = 0;
    foreach ($data as $row) {
        $i++;

        //data
        $c = '';
        $j = 0;
        $script .= '  [';
        foreach ($row as $key => $value) {
            $j++;
            if (is_numeric($value)) { $script .= $c.preg_replace('/[^0-9.-]/','',$value); } //remove commas
            else  { $script .= $c.'\''.addslashes($value).'\''; } //add quotes
            $c = ',';
        }
        $script .= '],'.$eol;
    }
    $script = substr($script,0,(strlen(','.$eol)*-1)); //get rid of that last comma
    $script .= $eol.' ]);'.$eol;
    
    //generate dashboard
    if ($dashboard) {
        $script .= ' var dashboard = new google.visualization.Dashboard(document.getElementById(\''.$chart_div.'_dashboard\'));'.$eol;
    }

    if ($category_filter) {
        if (!is_array($category_filter)) { return false; } //$category_filter must be an array with filterRowIndex and label set
        $script .= ' var categoryPicker = new google.visualization.ControlWrapper({'.$eol;
            $script .= '  \'controlType\': \'CategoryFilter\','.$eol;
            $script .= '  \'containerId\': \''.$chart_div.'_filter\','.$eol;
            $script .= '  \'options\': {'.$eol;
                $script .= '   \'filterRowIndex\': '.$category_filter['filterRowIndex'].','.$eol;
                $script .= '   \'ui\': {'.$eol;
                    $script .= '    \'labelStacking\': \'vertical\','.$eol;
                    $script .= '    \'label\': \''.$category_filter['label'].'\','.$eol;
                    $script .= '    \'allowTyping\': false,'.$eol;
                    $script .= '    \'allowMultiple\': true'.$eol;
                $script .= '    }'.$eol;
            $script .= '  }'.$eol;
        $script .= ' });'.$eol;
    }

    if (is_array($format_strings)) {
        $unique_formats = array(); //first, find all unique format patterns, and which columns they are associated with
        foreach ($format_strings as $column => $format) {
            if (array_key_exists($column,$column_numbers)) { //if data column itself exists
                $cn = $column_numbers[$column];
                if (!array_key_exists($format,$unique_formats)) { $unique_formats[$format] = array($cn); }
                else { $unique_formats[$format][] = $cn; }
            }
        }
        $i = 0;
        foreach ($unique_formats as $format => $columns) {    
            $i++;
            $script .= ' var formatter_'.$i.' = new google.visualization.NumberFormat({'.$format.'}); '.$eol; //define the format pattern
            foreach ($columns as $cn) { $script .= ' formatter_'.$i.'.format(data, '.$cn.');'.$eol; } //apply the pattern to all relevant columns
        }    
    }

    //SET UP OPTIONS
    $options = array();
    if ($responsive) { $options[] = 'width:chart_width'; }
    elseif ($width) { $options[] = 'width:'.$width; }
    if ($responsive) { $options[] = 'height:chart_height'; }
    elseif ($height) { $options[] = 'height:'.$height; }
    if ($title) { $options[] = 'title: \''.addslashes($title).'\''; }
    if (array_key_exists('fontName',$text_style) && ($text_style['fontName'])) { $options[] = 'fontName: \''.$text_style['fontName'].'\''; }
    if (array_key_exists('fontSize',$text_style) && ($text_style['fontSize'])) { $options[] = 'fontSize: '.$text_style['fontSize']; } //this should be numeric (pixels)

    $axes = array('x_axis'=>'hAxis','y_axis'=>'vAxis');
    foreach ($axes as $incoming => $outgoing) {
        $axis_options = array();
        if (${$incoming.'_title'}) { $axis_options[] = 'title: \''.addslashes(${$incoming.'_title'}).'\', titleTextStyle: {fontSize:12,italic:\'false\'}'; }
        if (${$incoming.'_baseline'} !== false) { $axis_options[] = 'baseline: '.${$incoming.'_baseline'}; }
        if (${$incoming.'_text_position'}) {
            $allowed = array('out','in','none');
            if (!in_array(${$incoming.'_text_position'},$allowed)) { custom_die('Invalid '.$incoming.'_text_position selected.'); }
            $axis_options[] = 'textPosition: \''.${$incoming.'_text_position'}.'\'';
        }
        $sub_options = array();
        if (array_key_exists('fontName',${$incoming.'_text_style'}) && (${$incoming.'_text_style'}['fontName'])) { $sub_options[] = 'fontName: \''.${$incoming.'_text_style'}['fontName'].'\''; }
        if (array_key_exists('fontSize',${$incoming.'_text_style'}) && (${$incoming.'_text_style'}['fontSize'])) { $sub_options[] = 'fontSize: '.${$incoming.'_text_style'}['fontSize']; } //this should be numeric (pixels)
        if (array_key_exists('color',${$incoming.'_text_style'}) && (${$incoming.'_text_style'}['color'])) { $sub_options[] = 'color: \''.${$incoming.'_text_style'}['color'].'\''; }
        if (array_key_exists('bold',${$incoming.'_text_style'}) && (${$incoming.'_text_style'}['bold'])) { $sub_options[] = 'bold: \'true\''; } elseif (array_key_exists('bold',${$incoming.'_text_style'})) { $sub_options[] = 'bold: \'false\''; } 
        if (array_key_exists('italic',${$incoming.'_text_style'}) && (${$incoming.'_text_style'}['italic'])) { $sub_options[] = 'italic: \'true\''; } elseif (array_key_exists('italic',${$incoming.'_text_style'})) { $sub_options[] = 'italic: \'false\''; } 
        if (count($sub_options) > 0) { $axis_options[] = 'textStyle: {'.implode(', ',$sub_options).'}'; }
        $sub_options = array();
        if (${$incoming.'_min'} !== false) { $sub_options[] = 'min: '.${$incoming.'_min'}; }
        if (${$incoming.'_max'} !== false) { $sub_options[] = 'max: '.${$incoming.'_max'}; }
        if (count($sub_options) > 0) { $axis_options[] = 'viewWindow: {'.implode(', ',$sub_options).'}'; }
        if (${$incoming.'_color'}) { $axis_options[] = 'baselineColor: \''.${$incoming.'_color'}.'\''; } //x_axis_color and y_axis_color
        $sub_options = array();
        if (${$incoming.'_gridlines'} !== false) { $sub_options[] = 'count: '.round(${$incoming.'_gridlines'}); }
        if (${$incoming.'_color'}) { $sub_options[] = 'color: \''.${$incoming.'_color'}.'\''; } //use axis color for gridlines too
        if (count($sub_options) > 0) { $axis_options[] = 'gridlines: {'.implode(', ',$sub_options).'}'; }
        if (count($axis_options) > 0) { $options[] = $outgoing.': {'.implode(', ',$axis_options).'}'; }
    }    

    $bc_options = array();
    if ($y_axis_title) { $bc_options[] = 'title: \''.addslashes($y_axis_title).'\', titleTextStyle: {fontSize:12,italic:\'false\'}'; }
    if ($background_color) { $bc_options[] = 'fill: \''.$background_color.'\''; }
    if ($border_color) { $bc_options[] = 'stroke: \''.$border_color.'\''; }
    if ($border_width) { $bc_options[] = 'strokeWidth: \''.$border_width.'\''; }
    if (count($bc_options) > 0) { $options[] = 'backgroundColor: {'.implode(', ',$bc_options).'}'; }

    if ($region) { $options[] = 'region: \''.$region.'\''; } //only works on geo charts
    if ($resolution) { $options[] = 'resolution: \''.$resolution.'\''; } //only works on geo charts
    if ($smooth) { $options[] = 'curveType: \'function\''; } //only works on line charts
    if ($bar_group_width) { $options[] = 'bar: {groupWidth: \''.$bar_group_width.'\'}'; }
    if (is_array($colors)) {
        $color_option = 'colors:[';
        $c = ''; foreach ($colors as $color) { $color_option .= $c.'\''.$color.'\''; $c = ','; }
        $color_option .= ']';
        if ($chart == 'GeoChart') { $color_option = 'colorAxis: {'.$color_option.'}'; }

        $options[] = $color_option;
    }

    if ($hide_legend) { $options[] = 'legend:\'none\''; }
    else {
        $legend_options = array();
        if ($legend_position) { $legend_options[] = 'position:\''.$legend_position.'\''; }
        if (count($legend_options) > 0) { $options[] = 'legend: {'.implode(', ',$legend_options).'}'; }
    }

    if (is_numeric($margins)) { $margins = array($margins,$margins,$margins,$margins); } //if a single value, set it for all 4 sides
    if ((is_array($margins)) && (count($margins) == 4)) {
        list($top,$right,$bottom,$left) = $margins;
        $chartarea = array();
        $chartarea['top'] = $top;
        $chartarea['left'] = $left;
        $chartarea['width'] = $width - $left - $right;
        $chartarea['height'] = $height - $top - $bottom;
        foreach ($chartarea as $key => $value) { $chartarea[$key] = $key.':'.$value; }
        $options[] = 'chartArea: {'.implode(',',$chartarea).'}';
    }
    
    if ($tooltips_html) { $options[] = 'tooltip:{isHtml:true}'; }

    $script .= ' var options = {'.implode(',',$options).'};'.$eol;
    
    if ($dashboard) {
        $script .= ' var chart = new google.visualization.ChartWrapper({'.$eol;
        $script .= '  \'chartType\': \''.$chart.'\','.$eol;
        $script .= '  \'containerId\': \''.$chart_div.'\','.$eol;
        $script .= '  \'options\': options'.$eol;
        $script .= ' });'.$eol;
        if ($category_filter) { $script .= ' dashboard.bind(categoryPicker, chart);'.$eol; }
        $script .= ' dashboard.draw(data);'.$eol;
    } else {
        $script .= ' var chart = new google.visualization.'.$chart.'(document.getElementById(\''.$chart_div.'\'));'.$eol;
        $script .= ' chart.draw(data, options);'.$eol;
    }
    $script .= '}'.$eol;
    $script .= '</script>'.$eol;

    $div = ''.$eol;
    if ($dashboard) { $div .= '<div id="'.$chart_div.'_dashboard">'.$eol; }
    if ($responsive) { $div_width = 300; } //this will get overwritten by JavaScript and is just here to prevent zooming on page load
    else { $div_width = $width; }
    $div .= '<div id="'.$chart_div.'" style="width:'.$div_width.'px;height:'.$height.'px;'.$div_style.'"></div> '.$eol;
    if ($category_filter) { $div .= '<div id="'.$chart_div.'_filter"></div>'.$eol; }
    if ($dashboard) { $div .= '</div>'.$eol; }
    if ($responsive) { //resize the div itself (with JavaScript)
        $div .= '<script type="text/javascript">'.$eol;
        $div .= ' var new_width = window.'.$chart_div.'_width + \'px\';'.$eol;
        $div .= ' var new_height = window.'.$chart_div.'_height + \'px\';'.$eol;
        $div .= ' document.getElementById(\''.$chart_div.'\').style.width = new_width;'.$eol;
        $div .= ' document.getElementById(\''.$chart_div.'\').style.height = new_height;'.$eol;
        $div .= '</script>'.$eol;
    }

    $html = '<html>'.$eol;
    $html .= '<head>'.$eol;
    $html .= $script;
    $html .= '</head>'.$eol;
    $html .= '<body>'.$eol;
    $html .= $div;
    $html .= '</body>'.$eol;
    $html .= '</html>'.$eol;
    return array('script'=>$script,'div'=>$div,'html'=>$html);
}

?>