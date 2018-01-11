<?php if ($free_form_hours == 1 && trim($free_form_hour_blob) != ""): ?>
<h1>Store Hours</h1>

    <div class="store_hours">
        <?php echo $free_form_hour_blob; ?>
    </div>

    <p>&nbsp;</p>

<?php elseif ($free_form_hours == 0 && ($monday_hours != "" || $tuesday_hours != "" || $wednesday_hours != "" || $thursday_hours != "" || $friday_hours != "" || $saturday_hours != "" || $sunday_hours != "" || trim($hours_note) != "")): ?>

    <h1>Store Hours</h1>

    <div class="store_hours">

<?php if ($monday_hours != ""): ?>
<div>
    <strong>Monday: </strong> <?php echo $monday_hours; ?>
</div>
        <?php endif; ?>
<?php if ($tuesday_hours != ""): ?>
<div>
    <strong>Tuesday: </strong> <?php echo $tuesday_hours; ?>
</div>
        <?php endif; ?>
<?php if ($wednesday_hours != ""): ?>
<div>
    <strong>Wednesday: </strong> <?php echo $wednesday_hours; ?>
</div>
        <?php endif; ?>
<?php if ($thursday_hours != ""): ?>
<div>
    <strong>Thursday: </strong> <?php echo $thursday_hours; ?>
</div>
        <?php endif; ?>
<?php if ($friday_hours != ""): ?>
<div>
    <strong>Friday: </strong> <?php echo $friday_hours; ?>
</div>
        <?php endif; ?>
<?php if ($saturday_hours != ""): ?>
<div>
    <strong>Saturday: </strong> <?php echo $saturday_hours; ?>
</div>
        <?php endif; ?>
<?php if ($sunday_hours != ""): ?>
<div>
    <strong>Sunday: </strong> <?php echo $sunday_hours; ?>
</div>
        <?php endif; ?>

<?php if (trim($hours_note) != ""): ?>
        <div class="hours_note">
            <?php echo $hours_note; ?>
        </div>
        <?php endif; ?>
    </div>


    <p>&nbsp;</p>

<?php endif; ?>

