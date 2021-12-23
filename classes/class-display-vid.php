<?php
/**
 * Display Vid
 *
 * @package WisVid\plugin\classes
 * @since 1.0.0
 * @author Adam Carter
 * @licence GNU-2.0+
 */
namespace WisVid\plugin\classes;

/**
 * Class Display Vid
 */
class Display_Vid {

	/**
	 * Class construct
	 */
	public function __construct() {
		add_filter( 'the_content', [$this, 'filter_home_content'], 1 );
		$this->video_control();
		add_action( 'wp_ajax_nopriv_ajaxlogin', [$this, 'ajax_login'] );
	}

	/**
	 * Filter home content
	 */
	public function filter_home_content() {
		if ( is_singular() && in_the_loop() && is_main_query() ) {
			return $content . $this->video_html();
		}
		return $content;
	}

	/**
	 * Video html
	 */
	public function video_html() {
		?>
			<header>
				<h2>WisVid</h2>
			</header>
			<div id="vid-div" class="example_video_wrapper">
				<!-- A responsive Wistia embed code -->
				<!-- 📣 Tip: Replace this with a video from your own Wistia account! Be sure to change the `id` in wistia-player-stuff.js to match your own video's id, too. -->
				<script src="https://fast.wistia.com/embed/medias/df2872v5dr.jsonp" async></script>
				<script src="https://fast.wistia.com/assets/external/E-v1.js" async></script>
				<div class="wistia_responsive_padding" style="padding:62.5% 0 0 0;position:relative;">
					<div class="wistia_responsive_wrapper" style="height:100%;left:0;position:absolute;top:0;width:100%;">
						<div class="wistia_embed wistia_async_df2872v5dr seo=false videoFoam=true" style="height:100%;width:100%">&nbsp;</div>
					</div>
				</div>
			</div>
			<script src="https://glitch-fun-pack.glitch.me/js/gfp.js"></script>
			<script>
				gfp.insertGlitchButton({ showTeamButton: true });
			</script>
		<?php
	}

	/**
	 * Video Control
	 */
	public function video_control() {
		?>
		<script>
		window._wq = window._wq || [];
		_wq.push({
		  id: "df2872v5dr",
		  options: {
			playerColor: "#f18003",
			wmode: "transparent"
		  },

		  // When the video becomes ready, we can run a function here, using `video` as a handle to the Player API.
		  // See all available events and methods at https://wistia.com/doc/player-api.
		  onReady: function (video) {
			video.bind("play", function () {
			  var playAlertElem = document.createElement("div");
			  playAlertElem.style.padding = "20px";
			  playAlertElem.innerHTML = `You played the video! Its name is ${video.name()}.`;
			  document.body.appendChild(playAlertElem);
			  return video.unbind;
			});

			video.bind("secondchange", (s) => {
			  var loggedIn = document.body.classList.contains( 'logged-in' );
			  if (s == 60 && loggedIn == false) {
				video.pause();
				//BTW ...the id is a palindrome
				var id = document.getElementById('vid-div');
				id.innerHTML += `
				  <div id="log-me-in">
					<h2>Howdy Partner, please log in!</h2>
					<?php wp_login_form(); ?>
				  </div>
				`;
			  }
			});

			video.bind("end", () => {
				console.log("The video ended");
			});
		  }
		});
		</script>
		<?php
	}

	public function login_form() {
		?>
		<form id="login" action="login" method="post">
			<h1>Site Login</h1>
			<p class="status"></p>
			<label for="username">Username</label>
			<input id="username" type="text" name="username">
			<label for="password">Password</label>
			<input id="password" type="password" name="password">
			<a class="lost" href="<?php echo wp_lostpassword_url(); ?>">Lost your password?</a>
			<input class="submit_button" type="submit" value="Login" name="submit">
			<a class="close" href="">(close)</a>
			<?php wp_nonce_field( 'ajax-login-nonce', 'security' ); ?>
		</form>
		<?php
	}

	public function ajax_login() {
		$info                  = array();
		$info['user_login']    = $_POST['username'];
		$info['user_password'] = $_POST['password'];
		$info['remember']      = true;
		$user_signon           = wp_signon( $info, false );

		if ( ! is_wp_error( $user_signon ) ){
			wp_set_current_user( $user_signon->ID );
			wp_set_auth_cookie( $user_signon->ID );
			echo json_encode( array( 'loggedin'=>true, 'message'=>__( 'Login successful, redirecting...' ) ) );
		}

		die();
	}
}
