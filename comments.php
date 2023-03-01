<?php
if ( post_password_required() ) {
	return;
}
?>
<div class="comments pb40 s-b-b clear">
<div class="inner">
	<?php
		$commenter = wp_get_current_commenter();
		$consent = empty(get_array_key($commenter, 'comment_author_email')) ? '' : ' checked="checked"';
		$comment_text_field = get_comment_text_field();
		$form_action_dir = get_brave_comment_config('form_action_dir');
		$comment_action = $form_action_dir ? $form_action_dir. '/wp-stop-spam.php' : '/wp-comments-post.php';
		$comment_form_array = array(
			'fields'  => array(
				'author'  => '<p class="commentform"><input type="text" name="author" id="author" class="inp cmt-input" placeholder="昵称[必填]" aria-required="true" size="30" value="' . esc_attr( $commenter['comment_author'] ) . '"></p>',
				'email'   => '<p class="commentform"><input type="text" name="email" id="email" class="inp cmt-input" placeholder="邮箱[必填·保密]" aria-required="true" size="30" value="' . esc_attr( $commenter['comment_author_email'] ) . '"></p>',
				'url'     => '<p class="commentform"><input type="text" name="url" id="url" class="inp cmt-input" placeholder="网址[选填]" size="30" value="'.$comment_author_url.'"></p>',
				'cookies' => '<p class="comment-form-cookies-consent"><input id="wp-comment-cookies-consent" name="wp-comment-cookies-consent" type="checkbox" value="yes"' . $consent . '/>' . '<label for="wp-comment-cookies-consent" class="c4">记住我的个人信息</label></p>',
			),
			'comment_field' 	   => '<textarea id="comment" name="comment"></textarea><textarea name= ' . '"' .$comment_text_field . '"' . ' class="little_star inp text-bg comment-textarea" placeholder="添加评论..." aria-required="true" cols="45" rows="8"></textarea>',
			'title_reply'   	   => '发表评论',
			'cancel_reply_link'    => '取消回复',
			'label_submit' 		   => '发表评论',
			'comment_notes_before' => '',
			'comment_notes_after'  => '',
			'class_submit' 		   => 'btn submit',
			'action'			   => $comment_action,
		);
		comment_form($comment_form_array);
	?>
	<?php if (!comments_open()) : ?>
		<p class="notice c6"><?php _e( '评论已关闭' , 'brave' ); ?></p>
	<?php endif; ?>
	<?php if ( have_comments() ) : ?>
		<h3 class="fwt pb10">
				<span class="i-comment mr-ico"></span>评论<?php
					printf( _n( '(1)', '(%1$s)', get_comments_number(), 'brave' ),
						number_format_i18n( get_comments_number() ), '' );
				?>
		</h3>

		<ol class="commentlist container">
			<?php wp_list_comments( array( 'callback' => 'brave_comment', 'style' => 'ol', 'short_ping' => true ) ); ?>
			<?php if ( get_comment_pages_count() > 1 && get_option( 'page_comments' ) ) : ?>
				<div class="next inner clear">
					<?php previous_comments_link(__('加载更多&hellip;', 'brave')); ?>
				</div>
			<?php endif; ?>
		</ol>
	<?php endif; // have_comments() ?>
</div>

</div><!-- #comments -->