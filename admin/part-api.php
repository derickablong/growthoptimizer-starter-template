<div class="setting-item">
    <div class="description">
        <h3>API Access Token</h3>
        
        <?php if ($is_active): ?>
            <p>Awesome! The site can now access the cloud-shared template blocks, global settings, loop items, forms, and other useful resources.</p>
        <?php else: ?>
            <p style="max-width:220px">Provide the API access token to access the template cloud server services.</p>
        <?php endif; ?>

        <div>
            <?php if ($is_active): ?>
                <span class="active-api-key-value"><?php echo $api_token.$api_token ?></span>
            <?php else: ?>
                <input type="text" id="api-token-input" value="" placeholder="Enter API access token" style="width:100%">
            <?php endif; ?>
        </div>
    </div>
    <div class="action">
        <?php 
        if ($is_active): do_action('go-lottie'); else: ?>
            <a href="#" class="button button-primary go-activate-api-token">Activate</a>
        <?php endif; ?>
    </div>
</div>