<?php 
/************************************************************************/
/* AContent                                                             */
/************************************************************************/
/* Copyright (c) 2013                                                   */
/* Inclusive Design Institute                                           */
/*                                                                      */
/* This program is free software. You can redistribute it and/or        */
/* modify it under the terms of the GNU General Public License          */
/* as published by the Free Software Foundation.                        */
/************************************************************************/
global $_current_user;
global $languageManager;

require_once(TR_INCLUDE_PATH.'classes/CoursesUtility.class.php');
?>

<form method="post" action="<?php echo $_SERVER['PHP_SELF'].'?_course_id='.$this->course_id; ?>" name="form">
<input type="hidden" name="_course_id" value="<?php echo $this->course_id; ?>" />
<?php if(isset( $_REQUEST['_struct_name'])) { 

 	echo '<input type="hidden" name="_struct_name" value="'. $_REQUEST['_struct_name']. '" />';
}
?>


<div class="input-form">
<fieldset class="group_form"><legend class="group_form"><?php echo _AT('course_property'); ?></legend>

	<table class="form-data" align="center">
		<tr>
			<td colspan="2" align="left"><br/><?php echo _AT('required_field_text') ;?></td>
		</tr>
		<?php if($_current_user->isAdmin()) { ?>
		<tr>
			<td align="left"><span class="required" title="<?php echo _AT('required_field'); ?>">*</span>
			<label for="this_author"><?php echo  _AT('assign_author'); ?></label>:</td>
			<td align="left">
			<select name="this_author" id="this_author">
			<?php
			foreach ($this->isauthor as $author){
				if($author['user_id'] == $this->course_row['user_id']){
	 				echo '<option value="'.$author['user_id'].'" selected="selected">'.$author['first_name'].' '.$author['last_name'].' ('.$author['login'].')</option>';
	 			}else{
	 				echo '<option value="'.$author['user_id'].'">'.$author['first_name'].' '.$author['last_name'].' ('.$author['login'].')</option>';	 		
	 			}
			}?>
			</select>
			</td>
		</tr>
		<?php } ?>

		<tr>
			<td align="left"><span class="required" title="<?php echo _AT('required_field'); ?>">*</span>
			<label for="title"><?php echo _AT('title'); ?></label>:</td>
			<td align="left"><input id="title" name="title" type="text" maxlength="255" size="45" value="<?php if (isset($_POST['login'])) echo stripslashes(htmlspecialchars($_POST['title'])); else echo stripslashes(htmlspecialchars($this->course_row['title'])); ?>" /></td>
		</tr>

		<tr>
			<td align="left"><label for="category"><?php  echo _AT('category_name'); ?></label></td>
			<td align="left">
			<select name="category_id" id="category">
				<?php if (isset($_POST['category_id'])) $category_id = $_POST['category_id'];
				      else $category_id = $this->course_row['category_id'];
				      CoursesUtility::printCourseCatsInDropDown($category_id); ?>
			</select>
			</td>
		</tr>

		<tr>
			<td align="left"><label for="pri_lang"><?php  echo _AT('primary_language'); ?></label></td>
			<td align="left"><?php $languageManager->printDropdown($this->course_row['primary_language'], 'pri_lang', 'pri_lang'); ?></td>
		</tr>

		<tr>
			<td align="left"><label for="description"><?php echo _AT('description'); ?></label></td>
			<td align="left"><textarea id="description" cols="45" rows="2" name="description"><?php if (isset($_POST['description'])) echo stripslashes(htmlspecialchars($_POST['description'])); else echo stripslashes(htmlspecialchars($this->course_row['description'])); ?></textarea></td>
		</tr>
                
                
		
                <tr>
                        <td align="left"><label for="copyright"><?php echo _AT('course_copyright'); ?></label></td>
                            <td><select id='copyright_index'>
                                <?php
                                    $xml = simplexml_load_file(TR_INCLUDE_PATH.'copyrights/copyrights.xml');
                                    $copyright_number = 0;
                                    foreach($xml->copyright as $copyright)
                                    {
                                        $copyright_number++;
                                        echo '<option value="copyright'.$copyright_number.'">'.$copyright->title.'</option>';
                                    }
                                ?>
                            </select></td>
                </tr>   
                <tr>
                        <td></td><td align="left"><textarea name="copyright" rows="6" cols="65" id="copyright" style="display:none">
                            <?php
                                if (isset($_POST['copyright']))
                                    echo stripslashes(htmlspecialchars($_POST['copyright']));
                                else echo stripslashes(htmlspecialchars($this->course_row['copyright']));
                            ?>
                        </textarea></td>
                </tr>
                
                <tr>
                        <td colspan="2" align="left">


                            <input type="checkbox" name="hide_course" id="hide_course" value="1" <?php if ($this->course_row['access'] == 'private') echo "checked"; ?> />


                            <label for="hide_course"><?php echo _AT('hide_course'); ?></label>



                        </td>


                </tr>
                <tr>
                        <td colspan="2">


                        <p class="submit_button">
                            <input type="submit" name="submit" value="<?php echo _AT('save'); ?>" class="submit" />
                            <input type="submit" name="cancel" value="<?php echo _AT('cancel'); ?>" class="submit" />

                        </p>
                        </td>
                </tr>
        </table>

</fieldset>
</div>
</form>

        <script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.3.2/jquery.min.js" ></script>
        <script type="text/javascript">
            $(document).ready(function() {
                $('#copyright_index').change(function () {
                    var isFirstSelected = $("#copyright_index option:first-child" ).is(':selected');
                    var isSecondSelected = $("#copyright_index option:first-child" ).next().is(':selected');
                    var isLastSelected = $("#copyright_index option:last-child" ).is(':selected');
                    var copyright_index_selected = $("#copyright_index option:selected").text();
                    
                    if (isFirstSelected || isSecondSelected) {
                        $('#copyright').hide();
                    } else {
                        $('#copyright').show();
                        $('#copyright').attr("readonly",isLastSelected?false:true);
                    }
                    
                    $.ajax({ type: "GET", url: "include/copyrights/copyrights.xml", dataType: "xml", success: function(xml) {
                        $(xml).find('copyright').each(function() {
                          if (copyright_index_selected === $(this).find('title').text()) {
                              $('#copyright').text($(this).find('text').text());
                          }
                        });
                      },
                      error: function(request, error, tipo_errore) { alert(error+': '+ tipo_errore); }
                    });
                });
            });
        </script>
