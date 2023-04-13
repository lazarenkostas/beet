<?php
$title = get_field('hero_title');
$hero_description = get_field('hero_description');
$hero_button = get_field('hero_button');
$hero_image = get_field('hero_image');
?>

<section class="hero-section">
	<div class="w-full flex flex-col-reverse lg:flex-row justify-between hero-container">
		<div class="hero-content w-full">
			<?php if (isset($title) && !empty($title)) : ?>
				<h2 class="hero-title mb-[42px]">
					<?php echo $title; ?>
				</h2>
			<?php endif; ?>
			<?php if (isset($hero_description) && !empty($hero_description)) : ?>
			<div class="hero-description mb-[33px]">
					<?php echo $hero_description; ?>
			</div>
			<?php endif; ?>
			<?php if (isset($hero_button) && !empty($hero_button)) : ?>
				<a class="custom-button" href="<?php echo $hero_button['url'] ?>"><?php echo $hero_button['title']?></a>
			<?php endif; ?>
		</div>
		<div class="hero-image bg-cover relative xl:bg-contain xl:bg-right xl:ml-auto bg-no-repeat h-full w-full min-h-[512px] " <?php if (!empty($hero_image)) : bg($hero_image); endif; ?>></div>
	</div>
</section>
