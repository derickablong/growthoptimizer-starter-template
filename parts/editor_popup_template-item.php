<div class="template-item <?php echo $template_terms ?>" data-template="<?php echo $index ?>" data-title="<?php echo strtolower($template['title']) ?>">
    <span class="zoom">
        <svg data-name="Layer 1" height="200" id="Layer_1" viewBox="0 0 200 200" width="200" xmlns="http://www.w3.org/2000/svg"><title/><path d="M126.5,84.25h-22v-22a10,10,0,0,0-20,0v22h-22a10,10,0,0,0,0,20h22v22a10,10,0,0,0,20,0v-22h22a10,10,0,0,0,0-20Z"/><path d="M154.5,140.75a77.3,77.3,0,0,0,16-47c.5-42.5-34-77-76.5-77a77,77,0,0,0,0,154,76.21,76.21,0,0,0,47-16l25.5,25.5c4,4,10,4,13.5,0a9.67,9.67,0,0,0,0-14Zm-60.5,10a57,57,0,1,1,57-57A57,57,0,0,1,94,150.75Z"/></svg>
    </span>
    <img src="<?php echo !empty($template['image']) ? $template['image'] : GROWTH_OPTIMIZER_URL . 'assets/img/default.png' ?>" alt="<?php echo $template['title'] ?>">
    <div class="item-title"><?php echo $template['title'] ?></div>
</div>