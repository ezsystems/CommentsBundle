<div class="block">
    {symfony_render(
        symfony_controller(
            'ez_comments.controller.render:renderForContent',
            hash( 'contentId', $node.contentobject_id )
        )
    )}
</div>
