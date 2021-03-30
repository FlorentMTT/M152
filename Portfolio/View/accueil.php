<!DOCTYPE html>
<html>

<head>
	<meta charset="utf-8" />
	<title>Fakebook</title>
	<link href="css/bootstrap.css" rel="stylesheet" />
	<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.11.2/css/all.css" rel="stylesheet"/>
</head>

<body>
	<main class="container">
		<?php require_once './View/nav.php'; ?>
		<div class="row">
			<!-- begin aside -->
			<aside class="col-5">
				<div class="card mx-auto" style="width: 18rem;">
					<img src="./media/img/bg_5.jpg" class="card-img-top" alt="blogPicture">
					<div class="card-body">
						<h5 class="card-title">Nom du blog</h5>
						<p class="card-text text-muted">Nb Followers, Nb Posts</p>
						<a href="#"><img src="./media/img/user.png" alt="profilePicture"></a>
					</div>
				</div>
			</aside>
			<!-- end aside -->
			<!-- begin section -->
			<section class="col-7">
				<div class="jumbotron">
					<h1 class="display-4">Welcome</h1>
				</div>
				<div id="posts">
					<?= showPosts($posts) ?>
				</div>
			</section>
			<!-- end section -->
		</div>
	</main>
	<div id="modalPost" class="modal" tabindex="-1">
		<div class="row">
			<!-- begin section -->
			<?php
			$isGoodType = true;
			$isTooBig = false;
			$commentaire = filter_input(INPUT_POST, 'commentaire', FILTER_SANITIZE_STRING);
			?>
			<section class="col-12">
				<form method="POST" action="" class="jumbotron col-7 mx-auto" enctype="multipart/form-data">
					<h2>Poster vos photos</h2>
					<label for="textarea">Commentaire</label>
					<textarea class="form-control" name="commentaire" id="textarea" placeholder="Ajouter un commentaire" required><?= $commentaire ?></textarea>
					<br />
					<div class="input-group mb-3">
						<div class="custom-file">
							<input type="file" id="multiFiles" multiple accept="image/*,video/*,audio/*" name="medias[]" class="custom-file-input" id="images" required />
							<label class="custom-file-label" for="images">Vos medias</label>
						</div>
					</div>
					<input type="button" id="upload" onclick="createPost()" value="Poster" class="btn btn-info" />
					<small class="text-danger">
						<?= !$isGoodType || $isTooBig ? 'Un ou plusieurs fichiers sont du mauvais type ou trop grand!' : ''; ?>
					</small>
				</form>
			</section>
			<!-- end section -->
		</div>
		<div name="preview" id="preview"></div>
	</div>
</body>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
<script type="text/javascript">
	function createPost() {
		var form_data = new FormData();
		var countFiles = document.getElementById('multiFiles').files.length;
		form_data.append('commentaire', document.getElementById('textarea').value);
		for (var index = 0; index < countFiles; index++) {
			form_data.append("medias[]", document.getElementById('multiFiles').files[index]);
		}

		$.ajax({
			type: 'post',
			url: './Controlleur/post.php',
			data: form_data,
			dataType: 'json',
			cache: false,
			contentType: false,
			processData: false,
			success: function(response) {
				$("#modalPost").modal("hide");
				var dataGetPost = new FormData();
				dataGetPost.append('getPosts', true);
				$.ajax({
					type: 'post',
					url: './Controlleur/accueil.php',
					data: dataGetPost,
					dataType: 'json',
					cache: false,
					contentType: false,
					processData: false,
					success: function(response) {
						document.getElementById("posts").innerHTML = response.responseText;
					},
					error: function(response) {
						document.getElementById("posts").innerHTML = response.responseText;
					},
				});
			},
			error: function(response) {
				console.log("erreur1");
			},
		});
	}
</script>

</html>