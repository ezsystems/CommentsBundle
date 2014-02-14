<div class="block">
    {symfony_render(
        symfony_controller(
            'ez_comments.controller.comments_renderer:renderForContent',
            hash( 'contentId', $node.contentobject_id )
        )
    )}
</div>
