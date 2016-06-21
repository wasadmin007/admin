<?php echo $header; ?>
<div id="content">
  <div class="breadcrumb">
    <?php foreach ($breadcrumbs as $breadcrumb) { ?>
    <?php echo $breadcrumb['separator']; ?><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a>
    <?php } ?>
  </div>
  <?php if ($error_warning) { ?>
  <div class="warning"><?php echo $error_warning; ?></div>
  <?php } ?>
  <div class="box">
    <div class="heading">
      <h1><img src="view/image/shipping.png" alt="" /> <?php echo $heading_title; ?></h1>
      <div class="buttons"><a onclick="$('#form').submit();" class="button"><?php echo $button_save; ?></a><a onclick="location = '<?php echo $cancel; ?>';" class="button"><?php echo $button_cancel; ?></a></div>
    </div>
    <div class="content">
      <div class="vtabs"><a href="#tab-general"><?php echo $tab_general; ?></a>
        <?php foreach ($geo_zones as $geo_zone) { ?>
        <a href="#tab-geo-zone<?php echo $geo_zone['geo_zone_id']; ?>"><?php echo $geo_zone['name']; ?></a>
        <?php } ?>
      </div>
      <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="form">
        <div id="tab-general" class="vtabs-content">
          <table class="form">
            <tr>
              <td><?php echo $entry_tax_class; ?></td>
              <td><select name="zonerates_tax_class_id">
                  <option value="0"><?php echo $text_none; ?></option>
                  <?php foreach ($tax_classes as $tax_class) { ?>
                  <?php if ($tax_class['tax_class_id'] == $zonerates_tax_class_id) { ?>
                  <option value="<?php echo $tax_class['tax_class_id']; ?>" selected="selected"><?php echo $tax_class['title']; ?></option>
                  <?php } else { ?>
                  <option value="<?php echo $tax_class['tax_class_id']; ?>"><?php echo $tax_class['title']; ?></option>
                  <?php } ?>
                  <?php } ?>
                </select></td>
            </tr>
            <tr>
              <td><?php echo $entry_status; ?></td>
              <td><select name="zonerates_status">
                  <?php if ($zonerates_status) { ?>
                  <option value="1" selected="selected"><?php echo $text_enabled; ?></option>
                  <option value="0"><?php echo $text_disabled; ?></option>
                  <?php } else { ?>
                  <option value="1"><?php echo $text_enabled; ?></option>
                  <option value="0" selected="selected"><?php echo $text_disabled; ?></option>
                  <?php } ?>
                </select></td>
            </tr>
            <tr>
              <td><?php echo $entry_calculation_method; ?></td>
              <td><?php if ($zonerates_calculation_method == 'item') { ?>
              <input type="radio" name="zonerates_calculation_method" value="item" checked="checked" />
              <?php echo $text_item; ?>
              <input type="radio" name="zonerates_calculation_method" value="weight" />
              <?php echo $text_weight; ?>
              <input type="radio" name="zonerates_calculation_method" value="price" />
              <?php echo $text_price; ?>
              <?php } elseif ($zonerates_calculation_method == 'weight') { ?>
              <input type="radio" name="zonerates_calculation_method" value="item" />
              <?php echo $text_item; ?>
              <input type="radio" name="zonerates_calculation_method" value="weight" checked="checked" />
              <?php echo $text_weight; ?>
              <input type="radio" name="zonerates_calculation_method" value="price" />
              <?php echo $text_price; ?>
              <?php } else { ?>
              <input type="radio" name="zonerates_calculation_method" value="item" />
              <?php echo $text_item; ?>
              <input type="radio" name="zonerates_calculation_method" value="weight" />
              <?php echo $text_weight; ?>
              <input type="radio" name="zonerates_calculation_method" value="price" checked="checked" />
              <?php echo $text_price; ?>
              <?php } ?></td>
            </tr>
            <tr>
              <td><?php echo $entry_sort_order; ?></td>
              <td><input type="text" name="zonerates_sort_order" value="<?php echo $zonerates_sort_order; ?>" size="1" /></td>
            </tr>
          </table>
        </div>
        <?php foreach ($geo_zones as $geo_zone) { ?>
        <div id="tab-geo-zone<?php echo $geo_zone['geo_zone_id']; ?>" class="vtabs-content">
          <table class="form">
            <tr>
              <td><?php echo $entry_rate; ?></td>
              <td><textarea name="zonerates_<?php echo $geo_zone['geo_zone_id']; ?>_rate" cols="40" rows="5"><?php echo ${'zonerates_' . $geo_zone['geo_zone_id'] . '_rate'}; ?></textarea></td>
            </tr>
            <tr>
              <td><?php echo $entry_status; ?></td>
              <td><select name="zonerates_<?php echo $geo_zone['geo_zone_id']; ?>_status">
                  <?php if (${'zonerates_' . $geo_zone['geo_zone_id'] . '_status'}) { ?>
                  <option value="1" selected="selected"><?php echo $text_enabled; ?></option>
                  <option value="0"><?php echo $text_disabled; ?></option>
                  <?php } else { ?>
                  <option value="1"><?php echo $text_enabled; ?></option>
                  <option value="0" selected="selected"><?php echo $text_disabled; ?></option>
                  <?php } ?>
                </select></td>
            </tr>
          </table>
        </div>
        <?php } ?>
      </form>
    </div>
  </div>
</div>
<script type="text/javascript"><!--
$('.vtabs a').tabs(); 
//--></script> 
<?php echo $footer; ?> 