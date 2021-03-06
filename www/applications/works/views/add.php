<?php if(!defined("_access")) die("Error: You don't have permission to access here..."); ?>
		
<?php
	if(isset($data)) {
		$ID  	     = recoverPOST("ID", 	      $data[0]["ID_Work"]);
		$title       = htmlentities(recoverPOST("title", $data[0]["Title"]));
		$description = recoverPOST("description", $data[0]["Description"]);
		$URL         = recoverPOST("URL",         $data[0]["URL"]);
		$image 	     = recoverPOST("image",       $data[0]["Image"]);
		$preview1 	 = recoverPOST("preview1",    $data[0]["Preview1"]);
		$preview2 	 = recoverPOST("preview2",    $data[0]["Preview2"]);
		$situation 	 = recoverPOST("state",       $data[0]["Situation"]);
		$edit        = TRUE;
		$action	     = "edit";
		$href	     = path($this->application . _sh . "cpanel" . _sh . "edit" . _sh . $ID);
	} else {
	    $ID  	     = 0;
		$title       = recoverPOST("title");
		$description = recoverPOST("description");
		$URL         = recoverPOST("URL");
		$situation   = recoverPOST("situation");
		$image 	     = NULL;
		$preview1 	 = NULL;
		$preview2 	 = NULL;
		$action	     = "save";
		$href	     = path($this->application . _sh . "cpanel" . _sh . "add" . _sh);
	}
?>

<div class="add-form">
	<form id="form-add" class="form-add" action="<?php echo $href; ?>" method="post" enctype="multipart/form-data">
		<fieldset>
			<legend><?php echo __("Add Work"); ?></legend>
			
			<p class="resalt">
				<?php echo __(ucfirst(whichApplication())); ?>
			</p>
			
			<?php echo isset($alert) ? $alert : NULL; ?>
			
			<p class="field">
				&raquo; <?php echo __("Title"); ?><br />
				<input id="title" name="title" type="text" value="<?php echo $title; ?>" tabindex="1" class="input required" />
			</p>
			
			<p class="field">
				&raquo; <?php echo __("URL"); ?><br />
				<input name="URL" type="text" value="<?php echo $URL; ?>" tabindex="4" class="input required" />
			</p>
		
			<p class="field">
				&raquo; <?php echo __("Description"); ?><br />
				<textarea id="editor" name="description" tabindex="2" class="textarea"><?php echo $description; ?></textarea>
			</p>
			
			<p class="field">
			
				&raquo; <?php echo __("Image"); ?><br />
				<input id="file1" name="image" type="file" tabindex="4" class="input required" />
				 
				<?php if($image) { ?>
					<a class="work-lightbox" title="<?php echo $title; ?>" href="<?php echo _webURL . _sh . $image;?>">
						<?php echo __("Preview"); ?>
					</a>
				<?php } ?>
			</p>
			
			<p class="field">
				&raquo; <?php echo __("Preview"); ?> 1<br />
				<input id="file2" name="preview1" type="file" tabindex="4" class="input required" />
				
				<?php if($preview1) { ?>
					<a class="work-lightbox" title="<?php echo $title; ?>" href="<?php echo _webURL . _sh . $preview1;?>">
						<?php echo __("Preview") ;?>
					</a>
				<?php } ?>
			</p>
			
			<p class="field">
				&raquo; <?php echo __("Preview"); ?> 2<br />
				<input id="file3" name="preview2" type="file" tabindex="4" class="input required" />
				<?php if($preview2) { ?>
					<a class="work-lightbox" title="<?php echo $title; ?>" href="<?php echo _webURL . _sh . $preview2;?>">
						<?php echo __("Preview");?>
					</a>
				<?php } ?>
			</p>
			
			<p class="field">
				&raquo; <?php echo __("Situation"); ?><br />
				<select id="situation" name="situation" size="1" tabindex="5" class="select">
					<option value="Active" <?php echo ($situation === "Active")  ? 'selected="selected"' : NULL; ?>>
						<?php echo __("Active"); ?>
					</option>
					<option value="Inactive" <?php echo ($situation === "Inactive")  ? 'selected="selected"' : NULL; ?>>
						<?php echo __("Inactive"); ?>
					</option>
				</select>
			</p>
			
			<p class="save-cancel">
				<input id="<?php echo $action; ?>" name="<?php echo $action; ?>" value="<?php echo __(ucfirst($action)); ?>" type="submit" class="submit save" tabindex="6" />
				<input id="cancel" name="cancel" value="<?php echo __("Cancel"); ?>" type="submit" class="submit cancel" tabindex="7" />
			</p>
			
			<input name="ID_Work" type="hidden" value="<?php echo $ID; ?>" />
		</fieldset>
	</form>
</div>

<?php 
	echo $this->js("lib/scripts/js/droparea.js");
	echo $this->js("droparea", "works");
	
	$this->CSS("style", segment(2), TRUE);