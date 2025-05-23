<div class="setting-item plugins">
    <div class="description">
        <h3>Install Plugins</h3>        
        <ul>
        <?php 
        $has_to_install = false;             
        foreach ($plugins as $folder => $info):                         
            if (array_key_exists( $info['file'], $installed_plugins )) continue;

            $id = sanitize_title( $folder );
            $has_to_install = true;
            ?>
            <li class="not-installed">
                <label for="<?php echo $id ?>">
                    <input type="checkbox" class="plugin-item" id="<?php echo $id ?>" value="<?php echo $folder ?>">                    
                    <span><?php echo $info['name'] ?></span>
                </label>                
            </li>
        <?php endforeach; ?>

        <?php 
        foreach ($plugins as $info): 
            if (!array_key_exists( $info['file'], $installed_plugins )) continue;
            ?>
            <li class="installed"><?php echo $info['name'] ?></li>
        <?php endforeach; ?>

        </ul>

        <div class="notes">
            <span>
                <svg viewBox="0 0 64 64" xmlns="http://www.w3.org/2000/svg" width="20"><defs><style>.cls-1{fill:#fcb316;}.cls-2{fill:#ffcb29;}.cls-3{fill:#ef5451;}.cls-4{fill:#263238;}</style></defs><g id="Icon"><path class="cls-1" d="M60,32A28.02,28.02,0,0,1,34,59.92c-.66.06-1.33.08-2,.08A28,28,0,0,1,32,4c.67,0,1.34.02,2,.08A28.02,28.02,0,0,1,60,32Z"/><ellipse class="cls-2" cx="34" cy="32" rx="26" ry="27.92"/><path class="cls-3" d="M32,43.786a21.482,21.482,0,0,1-11.537-3.345,1,1,0,1,1,1.074-1.687,19.56,19.56,0,0,0,20.926,0,1,1,0,1,1,1.074,1.687A21.482,21.482,0,0,1,32,43.786Z"/><path class="cls-4" d="M50,20.2H14a1,1,0,0,0-1,1v10a3,3,0,0,0,3,3h8a3,3,0,0,0,3-3v-5H37v5a3,3,0,0,0,3,3h8a3,3,0,0,0,3-3v-10A1,1,0,0,0,50,20.2Z"/></g></svg>
            </span>
            <div><strong>Note:</strong> <span>Elementor Pro</span>, <span>Gravity Forms</span>, <span>Advance Custom Field Pro</span> and <span>Ultimate Addons for Elementor Pro</span> needs to be manually activated with the license key. Some templates will not work if the license key is not provided.</div>
        </div>

    </div>
    <div class="action">
        <?php if ($has_to_install): ?>
            <a href="#" class="button button-primary go-install-plugins">Install</a>        
        <?php else: do_action('go-lottie'); endif; ?>                
    </div>
</div>