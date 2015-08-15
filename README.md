# SoundPress
A WordPress plugin to embed Soundcloud tracks in custom posts, perfect for podcasters and musicians.

SoundPress appends a Soundcloud embedded object to the bottom of your post. SoundPress also provides you with the ability to access Soundcloud API endpoints to use in your themes such as download links and durations of your tracks.

### How to use SoundPress
* Install SoundPress in your WordPress plugin folder and activate. You will now see the SoundPress custom post type in your wp-admin.
* In the SoundPress Settings accessed via Settings  > SoundPress enter your Soundcloud OAuth Client ID and OAuth Secret and hit **Save Changes**. You will notice in  the SoundPress Settings the option to *"Append oembed"*. Selecting this option will append a Soundcloud embedded object to the bottom of your SoundPress custom posts providing you have supplied a Soundcloud URL to retrieve the track from.
* To supply a Soundcloud URL to retrieve the track just paste the relevant Soundcloud URL in the *"Soundcloud Link"* meta box in the SoundPress Add Post.
* You will now see an embedded Soundcloud object on the post and also be able to access the [Soundcloud API endpoints](https://developers.soundcloud.com/docs/api/reference#tracks).
* Access to the API endpoints is useful for providing users with a direct link to download the audio file or display the duration of the track on the post, for example - perfect for podcast sites!

 