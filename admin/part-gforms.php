<div class="setting-item loop-items">
    <div class="description">
        <h3>Import Gravity Forms</h3> 
        
        <ul>
            <?php 
            $has_to_install = false;
            foreach($gform_items as $index => $item): 
                if (in_array($item['form_id'], $imported)) continue;
                $has_to_install = true;
            ?>
            <li>
                <label for="gform-item-<?php echo $item['form_id'] ?>">
                    <input type="checkbox" class="gform-item" id="gform-item-<?php echo $item['form_id'] ?>" value="<?php echo $index ?>" data-name="<?php echo $item['title'] ?>">                    
                    <span><?php echo $item['title'] ?></span>
                </label>                
            </li>
            <?php endforeach; ?>

            <?php 
            foreach ($gform_items as $item): 
                if (!in_array( $item['form_id'], $imported )) continue;
                ?>
                <li class="installed"><?php echo $item['title'] ?></li>
            <?php endforeach; ?>
        </ul>      
    </div>
    <div class="action completed">
        <?php if ($has_to_install): ?>
            <a href="#" class="button button-primary go-import-gforms">Import</a> 
        <?php else: do_action('go-lottie'); endif; ?>                
    </div>
</div>