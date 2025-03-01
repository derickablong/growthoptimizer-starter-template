<div class="setting-item loop-items">
    <div class="description">
        <h3>Import Elementor Loop</h3> 
        
        <ul>
            <?php 
            $has_to_install = false;
            foreach($loop_items as $index => $item): 
                if (array_key_exists($item['ID'], $imported)) continue;
                $has_to_install = true;
            ?>
            <li>
                <label for="loop-item-<?php echo $item['ID'] ?>">
                    <input type="checkbox" class="loop-item" id="loop-item-<?php echo $item['ID'] ?>" value="<?php echo $index ?>" data-name="<?php echo $item['title'] ?>">                    
                    <span><?php echo $item['title'] ?></span>
                </label>                
            </li>
            <?php endforeach; ?>

            <?php 
            foreach ($loop_items as $item): 
                if (!array_key_exists( $item['ID'], $imported )) continue;
                ?>
                <li class="installed no-bullet"><span class="post-id" data-id="<?php echo $imported[$item['ID']] ?>">ID: <?php echo $imported[$item['ID']] ?></span> <?php echo $item['title'] ?></li>
            <?php endforeach; ?>
        </ul>      
    </div>
    <div class="action">
        <?php if ($has_to_install): ?>
            <a href="#" class="button button-primary go-import-loop-items">Import</a>       
        <?php else: do_action('go-lottie'); endif; ?>                
    </div>
</div>