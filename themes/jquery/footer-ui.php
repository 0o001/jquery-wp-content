	</div>
</div>
<footer class="clearfix simple">
	<div class="constrain">
		<div class="row">
			<div class="six columns offset-by-three">
				<h3><span>Books</span></h3>
				<ul class="books">
					<li>
						<a href="https://www.manning.com/books/jquery-ui-in-action">
							<span><img src="<?php echo get_template_directory_uri(); ?>/content/books/jquery-ui-in-action.jpg" alt="jQuery UI in Action by TJ VanToll" width="92" height="114"></span>
							<strong>jQuery UI in Action</strong><br>
							<cite>TJ VanToll</cite>
						</a>
					</li>
					<li>
						<a href="https://www.packtpub.com/web-development/jquery-ui-themes-beginners-guide">
							<img src="<?php echo get_template_directory_uri(); ?>/content/books/jquery-ui-themes.jpg" alt="jQuery UI Themes by Adam Boduch" width="92" height="114">
							<span class="book-title">jQuery UI Themes</span>
							<cite>Adam Boduch</cite>
						</a>
					</li>
					<li>
						<a href="https://www.packtpub.com/web-development/jquery-ui-cookbook">
							<img src="<?php echo get_template_directory_uri(); ?>/content/books/jquery-ui-cookbook.jpg" alt="jQuery UI Cookbook by Adam Boduch" width="92" height="114">
							<span class="book-title">jQuery UI Cookbook</span>
							<cite>Adam Boduch</cite>
						</a>
					</li>
				</ul>
			</div>
		</div>

		<?php get_template_part( 'footer', 'bottom' ); ?>
	</div>
</footer>

<?php wp_footer(); ?>
<?php jq_the_algolia_docsearch(); ?>

</body>
</html>
