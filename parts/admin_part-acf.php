<div class="setting-item loop-items">
    <div class="description">
        <h3>Import ACF</h3> 
        
        <ul>
            <?php 
            $has_to_install = false;
            foreach($acf_items as $index => $item): 
                if (in_array($item['ID'], $imported)) continue;
                $has_to_install = true;
            ?>
            <li>
                <label for="acf-item-<?php echo $item['ID'] ?>">
                    <input type="checkbox" class="acf-item" id="acf-item-<?php echo $item['ID'] ?>" value="<?php echo $index ?>" data-name="<?php echo $item['post_title'] ?>">                    
                    <span><?php echo $item['post_title'] ?></span>
                </label>                
            </li>
            <?php endforeach; ?>

            <?php 
            foreach ($acf_items as $item): 
                if (!in_array( $item['ID'], $imported )) continue;
                ?>
                <li class="installed"><?php echo $item['post_title'] ?></li>
            <?php endforeach; ?>
        </ul>        
    </div>
    <div class="action">
        <?php if ($has_to_install): ?>
            <a href="#" class="button button-primary go-import-acf-items">Import</a>        
        <?php else: do_action('go-lottie'); endif; ?>                
    </div>
</div>