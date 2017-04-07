<?php

$options = implode("\n", array_map(function($x) {
    return "<option value='" . $x['name'] . "'>" . $x['label'] . '</option>';
}, $columns));

?>
<div class="content_wrap">
    <div class="content">

        <h1><i class="fa fa-upload"></i>&nbsp;File Upload - <?php echo $productupload["original_filename"]; ?> - Map Columns</h1>
        <p><b>Please map the columns from your CSV file to the known column list.</b></p>
        <br>

        <!-- VALIDATION ALERT -->
        <?php if (@$errors): ?>
            <div class="validation_error" id="login_validation_error">
                <img src="<?php echo $assets; ?>/images/error.png" style="float:left;margin-right:10px;">
                <h1>Error</h1>
                <div class="clear"></div>
                <p><?php echo $errors; ?></p>

            </div>
        <?php endif; ?>
        <!-- END VALIDATION ALERT -->

        <!-- SUCCESS -->
        <?php if(@$success): ?>
            <div class="success">
                <h1><span style="color:#090;"><i class="fa fa-check"></i></span>&nbsp;Success</h1>
                <div class="clear"></div>
                <p><?php echo $success; ?></p>
            </div>
        <?php endif; ?>
        <!-- END SUCCESS -->

        <form method="post" id="matchingform" action="<?php echo base_url('adminproductuploader/save_matchcolumns/' . $productupload_id); ?>">
        <table width="100%">
            <thead>
                <tr>
                    <th><strong>Header Row</strong></th>
                    <th><strong>First Row</strong></th>
                    <th><strong>Match Column</strong></th>
                </tr>
            </thead>
            <tbody>
            <?php for ($i = 0; $i < count($header); $i++): ?>
                <tr class="row-<?php echo $i; ?>">
                    <td><strong class="header-<?php echo $i; ?>"><?php echo $header[$i]; ?></strong></td>
                    <td valign="center"><?php if (count($firstrow) > $i): ?><?php if (strlen($firstrow[$i]) > 200): ?><?php echo substr($firstrow[$i], 0, 200); ?><?php else: ?><?php echo $firstrow[$i]; ?><?php endif; ?><?php endif; ?></td>
                    <td><select name="column_<?php echo $i; ?>">
                            <option value="">-- Ignore Column --</option>
                            <?php echo $options; ?>
                        </select></td>
                </tr>
            <?php endfor; ?>
            </tbody>
        </table>

            <a href="#" id="button" onClick="$('#matchingform').submit(); return false">Save and Apply Column Mapping</a>

        </form>



    </div>
</div>

<script type="application/javascript">
    (function() {
        var header_count = <?php echo count($header); ?>;
        var assigned_columns = <?php echo json_encode($existing_mapping); ?>;
        var columns = <?php echo json_encode($columns); ?>;
        var matchesFound = {};


        if (Array.isArray(assigned_columns) && assigned_columns.length > 0) {
            for (var i = 0; i < header_count; i++) {
                if (i < assigned_columns.length) {
                    $("select[name='column_" + i + "'] option[value='" + assigned_columns[i] + "']").prop("selected", true);
                }
            }

        } else {
            var autoMatchFn = function(index) {
                var header = $(".header-" + index).text();
                var match = false;
                for (var j = 0; j < columns.length; j++) {
                    if (!match) {
                        if (header.toLowerCase() == columns[j].name.toLowerCase() || header.toLowerCase() == columns[j].label.toLowerCase() || (columns[j].alternates && Array.isArray(columns[j].alternates) && (-1 < columns[j].alternates.indexOf(header) || -1 < columns[j].alternates.indexOf(header.toLowerCase())) )) {
                            if (columns[j].multiple || !matchesFound[columns[j].name]) {
                                // convert them.
                                $("select[name='column_" + index + "'] option[value='" + columns[j].name + "']").prop("selected", true);

                                matchesFound[columns[j].name] = true;
                            }
                        }
                    }
                }
            };

            for (var i = 0; i < header_count; i++) {
                autoMatchFn(i);
            }

        }


    })();


</script>

