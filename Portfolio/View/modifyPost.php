<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8"/>
	<title>Fakebook</title>
	<link href="./css/bootstrap.css" rel="stylesheet"/>
	<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.11.2/css/all.css" rel="stylesheet"/>
</head>
<body>
	<main class="container">
		<?php require_once './View/nav.php'; ?>
		<div class="row">
			<!-- begin section -->
			<section class="col-12">
				<form method="POST" action="" class="jumbotron col-7 mx-auto" enctype="multipart/form-data">
					<h2>Modifier votre post</h2>
					<label for="textarea">Commentaire</label>
					<textarea class="form-control" name="commentaire" id="textarea" placeholder="Ajouter un commentaire" required><?= $post['commentaire'] ?></textarea>
					<br/>
					<hr>
          <?php
					$i = 0;
          foreach ($post['medias'] as $media) {
            ?>
            <div class="row">
							<label class="col-8" for="<?= $i ?>">
								<?php
								$output = '';
								$type = explode('/', $media['typeMedia'])[0];
					      if ($type == 'image') {
					        $output .= '<img src="./media/' . $media['nomMedia'] . '" class="figure-img img-fluid" alt=""/>&nbsp;';
					      } else {
					        $output .= '<' . $type . ' class="col-12';
					        $output .= $type == 'video' ? ' img-fluid" autoplay loop' : '"';
					        $output .= ' controls><source src="./media/' . $media['nomMedia'] . '" type="' . $media['typeMedia'] . '"/></' . $type . '>';
					      }
								echo $output;
								?>
							</label>
							<div class="align-middle">
								<input type="checkbox" name="<?= $i ?>" id="<?= $i ?>"/>&nbsp;
								<label for="<?= $i++ ?>">Supprimer</label>
							</div>
						</div>
						<hr>
            <?php
          }
          ?>
					<div class="input-group mb-3">
						<div class="custom-file">
							<input type="file" multiple accept="image/*,video/*,audio/*" name="medias[]" class="custom-file-input" id="images"/>
							<label class="custom-file-label" for="images">Ajouter des medias</label>
						</div>
					</div>
					<input type="submit" value="Modifier" class="btn btn-info"/>
				</form>
			</section>
			<!-- end section -->
		</div>
	</main>
</body>
</html>
