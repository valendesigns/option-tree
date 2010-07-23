<?php
/**
 *
 * Text Block Option
 *
 */
function option_tree_textblock($value, $settings) { ?>
  <div class="option option-textblock">
    <h3 class="text-title"><?php echo $value->item_title; ?></h3>
    <div class="section">
      <div class="text_block">
        <?php echo html_entity_decode($value->item_desc); ?>
      </div>
    </div>
  </div>
<?php
}