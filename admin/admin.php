<div class="wrap">
    <div class="settings">          
        <div class="setting-tabs">
            <a href="<?php echo GROWTH_OPTIMIZER_ADMIN_PAGE ?>-api" class="tab-api <?php echo $tab=='api'?'active':'default' ?>">
                <svg width="30" class="icon icon-tabler icon-tabler-api" fill="none" height="24" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" viewBox="0 0 24 24" width="24" xmlns="http://www.w3.org/2000/svg"><path d="M0 0h24v24H0z" fill="none" stroke="none"/><path d="M4 13h5"/><path d="M12 16v-8h3a2 2 0 0 1 2 2v1a2 2 0 0 1 -2 2h-3"/><path d="M20 8v8"/><path d="M9 16v-5.5a2.5 2.5 0 0 0 -5 0v5.5"/></svg>                
            </a>
            <?php if ($is_active): ?>
                <a href="<?php echo GROWTH_OPTIMIZER_ADMIN_PAGE ?>-global-settings" class="<?php echo $tab=='global-settings'?'active':'default' ?>">
                    <svg width="30" class="icon icon-tabler icon-tabler-brand-codesandbox" fill="none" height="24" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" viewBox="0 0 24 24" width="24" xmlns="http://www.w3.org/2000/svg"><path d="M0 0h24v24H0z" fill="none" stroke="none"/><path d="M20 7.5v9l-4 2.25l-4 2.25l-4 -2.25l-4 -2.25v-9l4 -2.25l4 -2.25l4 2.25z"/><path d="M12 12l4 -2.25l4 -2.25"/><line x1="12" x2="12" y1="12" y2="21"/><path d="M12 12l-4 -2.25l-4 -2.25"/><path d="M20 12l-4 2v4.75"/><path d="M4 12l4 2l0 4.75"/><path d="M8 5.25l4 2.25l4 -2.25"/></svg>
                    <span>Global Settings</span>
                </a>
                <a href="<?php echo GROWTH_OPTIMIZER_ADMIN_PAGE ?>-plugins" class="<?php echo $tab=='plugins'?'active':'default' ?>">
                    <svg width="30" class="icon icon-tabler icon-tabler-plug" fill="none" height="24" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" viewBox="0 0 24 24" width="24" xmlns="http://www.w3.org/2000/svg"><path d="M0 0h24v24H0z" fill="none" stroke="none"/><path d="M10 6l8 8l-2 2a5.657 5.657 0 1 1 -8 -8l2 -2z"/><path d="M4 20l4 -4"/><path d="M15 4l-3.5 3.5"/><path d="M20 9l-3.5 3.5"/></svg>
                    <span>Plugins</span>
                </a>
                <a href="<?php echo GROWTH_OPTIMIZER_ADMIN_PAGE ?>-loop-items" class="<?php echo $tab=='loop-items'?'active':'default' ?>">
                    <svg width="30" class="icon icon-tabler icon-tabler-brand-windows" fill="none" height="24" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" viewBox="0 0 24 24" width="24" xmlns="http://www.w3.org/2000/svg"><path d="M0 0h24v24H0z" fill="none" stroke="none"/><path d="M17.8 20l-12 -1.5c-1 -.1 -1.8 -.9 -1.8 -1.9v-9.2c0 -1 .8 -1.8 1.8 -1.9l12 -1.5c1.2 -.1 2.2 .8 2.2 1.9v12.1c0 1.2 -1.1 2.1 -2.2 1.9z"/><line x1="12" x2="12" y1="5" y2="19"/><line x1="4" x2="20" y1="12" y2="12"/></svg>
                    <span>Loop Items</span>
                </a>
                <?php if (defined('GF_MIN_WP_VERSION')): ?>
                <a href="<?php echo GROWTH_OPTIMIZER_ADMIN_PAGE ?>-gforms" class="<?php echo $tab=='gforms'?'active':'default' ?>">
                    <svg width="30" class="icon icon-tabler icon-tabler-layout-board-split" fill="none" height="24" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" viewBox="0 0 24 24" width="24" xmlns="http://www.w3.org/2000/svg"><path d="M0 0h24v24H0z" fill="none" stroke="none"/><rect height="16" rx="2" width="16" x="4" y="4"/><path d="M4 12h8"/><path d="M12 15h8"/><path d="M12 9h8"/><path d="M12 4v16"/></svg>
                    <span>Gravity Forms</span>
                </a>
                <?php endif; ?>
                <?php if(class_exists( 'ACF' )): ?>
                <a href="<?php echo GROWTH_OPTIMIZER_ADMIN_PAGE ?>-acf" class="<?php echo $tab=='acf'?'active':'default' ?>">
                    <svg width="30" class="icon icon-tabler icon-tabler-layers-subtract" fill="none" height="24" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" viewBox="0 0 24 24" width="24" xmlns="http://www.w3.org/2000/svg"><path d="M0 0h24v24H0z" fill="none" stroke="none"/><rect height="12" rx="2" width="12" x="8" y="4"/><path d="M16 16v2a2 2 0 0 1 -2 2h-8a2 2 0 0 1 -2 -2v-8a2 2 0 0 1 2 -2h2"/></svg>
                    <span>ACF</span>
                </a>
                <?php endif; ?>                                              
            <?php endif; ?>
        </div>
        <div class="go-settings">
        <?php
        switch ($tab):
            case 'global-settings': do_action('admin-part-global-settings'); break;
            case 'plugins': do_action('admin-part-plugins'); break;
            case 'loop-items': do_action('admin-part-loop-items'); break;
            case 'acf': do_action('admin-part-acf'); break;                
            case 'gforms': do_action('admin-part-gforms'); break;                
            default: do_action('admin-part-api', $api_key_token, $is_active);
        endswitch;        
        ?>
        </div>        
    </div>
    <script src="https://unpkg.com/@dotlottie/player-component@2.7.12/dist/dotlottie-player.mjs" type="module"></script>
</div>