<?php

class component_ContentSocialSharing extends SK_Component
{

    public function __construct( array $params = null )
    {
        parent::__construct( 'content_social_sharing' );
        $this->document_meta = new SK_HttpDocumentMeta();
    }

    public function prepare(SK_Layout $Layout, SK_Frontend $Frontend)
    {
        $feature = get_class( $Layout->current_component_parent() );

        if ( !in_array( $feature, array( 'component_BlogPostView', 'component_PhotoView', 'component_VideoView' ) ) )
        {
            $this->annul();
            return;
        }

        $facebook_share_config = SK_Config::section( 'share.facebook_share' );
        $twitter_share_config = SK_Config::section( 'share.twitter_share' );
        $google_share_config = SK_Config::section( 'share.google_share' );

        if ( (bool)$facebook_share_config->enabled || (bool)$twitter_share_config->enabled || (bool)$google_share_config->enabled )
        {
            preg_match( '/blog|photo|video/i', $feature, $matches );
            $content = strtolower( $matches[0] );

            switch ( $content )
            {
                case 'photo':
                    $data = app_ProfilePhoto::getPhotoInfo( SK_HttpRequest::$GET['photo_id']);
                    $data['img'] = $data['src'];
                    $data['href'] = SK_Navigation::href( 'profile_photo', array( 'photo_id' => SK_HttpRequest::$GET['photo_id'] ) );
                    $data['desc'] = $data['html_description'];
                    break;

                case 'blog':
                    $data = app_ContentSocialSharing::getBlogPostById( SK_HttpRequest::$GET['postId'] );
                    $data['img'] = app_ProfilePhoto::getThumbUrl( $data['profile_id'] );
                    $data['desc'] = $data['text'];
                    break;

                case 'video':
                    $data = app_ContentSocialSharing::getVideoByHash( SK_HttpRequest::$GET['videokey'] );
                    $data['img'] =  $data['video_url'] ;
                    $this->document_meta->addMeta( 'og:video', $data['video_url'], 'property' );
                    $data['desc'] = $data['description'];
            }
            
            // Facebook
            if ( (bool)$facebook_share_config->enabled )
            {
//                $Frontend->include_js_file( FBC_Service::getInstance()->getFaceBookAccessDetails()->jsLibUrl );
  $Frontend->onload_js('(function(d, s, id)
 {
  if ( window.fbInited ) return false;
  var js, fjs = d.getElementsByTagName(s)[0];
  if (d.getElementById(id)) return;
  js = d.createElement(s); js.id = id;
  js.src = "//connect.facebook.net/en_US/all.js#xfbml=1&appId='.$facebook_share_config->app_id.'";
  fjs.parentNode.insertBefore(js, fjs);
}(document, "script", "facebook-jssdk"));');

                $this->document_meta->addMeta( 'og:title', $data['title'], 'property' );
                $this->document_meta->addMeta( 'og:type', 'website', 'property' );
                $this->document_meta->addMeta( 'og:image', $data['img'], 'property' );
                $this->document_meta->addMeta( 'og:description', $data['desc'], 'property' );
                $this->document_meta->addMeta( 'og:site_name', SK_Config::section( 'site.official' )->site_name, 'property' );
                $this->document_meta->addMeta( 'fb:app_id', $facebook_share_config->app_id, 'property' );

                $Layout->assign( 'fb_enabled', true );
                $Layout->assign( 'fb_send', (bool)$facebook_share_config->send_button ? 'true' : '' );
                $Layout->assign( 'fb_layout', $facebook_share_config->layout_style );
                $Layout->assign( 'fb_width', $facebook_share_config->width );
                $Layout->assign( 'fb_show_faces', (bool)$facebook_share_config->show_faces  ? 'true' : '' );
                $Layout->assign( 'fb_action', $facebook_share_config->verb_to_display );
                $Layout->assign( 'fb_font', $facebook_share_config->font );
                $Layout->assign( 'fb_color_scheme', $facebook_share_config->color_scheme );
            }

            // Twitter
            if ( (bool)$twitter_share_config->enabled )
            {
                $Frontend->include_js_file( 'http://platform.twitter.com/widgets.js' );
                $Layout->assign( 'tw_enabled', true );
                $Layout->assign( 'tw_href', $_SERVER['REQUEST_URI'] );

                if ( SK_HttpUser::is_authenticated() )
                {
                    $Layout->assign( 'tw_data_via', SK_HttpUser::username() );
                }

                $Layout->assign( 'tw_data_count', (bool)$twitter_share_config->show_count );
                $Layout->assign( 'tw_data_size', (bool)$twitter_share_config->large_button );
                $Layout->assign( 'data_dnt', (bool)$twitter_share_config->opt_out );
            }

            // g +1
            if ( (bool)$google_share_config->enabled )
            {
                $Frontend->include_js_file( 'https://apis.google.com/js/plusone.js');

                $this->document_meta->addMeta( 'name', $data['title'], 'itemprop' );
                $this->document_meta->addMeta( 'description', $data['desc'], 'itemprop' );
                $this->document_meta->addMeta( 'image', $data['img'], 'itemprop' );

                $Layout->assign( 'gp_enabled', true );
                $Layout->assign( 'gp_size', $google_share_config->size );
                $Layout->assign( 'gp_annotation', $google_share_config->annotation );
                $Layout->assign( 'gp_width', $google_share_config->width );
            }
        }
        else
        {
            $this->annul();
            return;
        }

        parent::prepare($Layout, $Frontend);
    }
}
