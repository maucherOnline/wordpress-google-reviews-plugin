<?php

if (! defined('ABSPATH'))
	exit;
?>

<style>
    #g-review {
        display: grid;
        grid-template-columns: <?php echo esc_html($columns_css); ?>;
        gap: 32px;
        margin: 30px 0;
    }
    #g-review .g-review {
        padding: 24px;
        background: #FFFFFF 0% 0% no-repeat padding-box;
        box-shadow: 0px 7px 20px #00000029;
        border-radius: 16px;
        opacity: 1;
    }
    #g-review .gr-inner-header {
        display: flex;
        flex-flow: row wrap;
        position: relative;
    }
    #g-review .gr-inner-header p {
        margin: 0;
        flex-basis: calc(100% - 60px);
        font-size: 16px;
        line-height: 1.5;
    }
    #g-review .gr-inner-header p a{
        text-decoration: none;
        font: normal normal bold 18px/22px Roboto;
        letter-spacing: 0px;
        color: #5791FF;
    }
    #g-review .gr-inner-header img.gr-profile {
        margin: 0 10px 10px 0;
    }
    #g-review .gr-inner-header img.gr-google {
        position: absolute;
        right: 0;
        top: 0;
        width: 18px;
        height: 18px;
    }

    #g-review .g-review .gr-stars img {
        display: inline-block !important;
        width: 18px !important;
        height: 18px !important;
        margin: 0 10px 0 0 !important;
        vertical-align: middle !important;
    }
    #g-review .g-review .gr-stars .time{
        vertical-align: middle;
        margin-left: 15px;
        font: normal normal normal 14px/17px Roboto;
        letter-spacing: 0px;
        color: #0000004D;
    }

    #g-review .gr-inner-body{
        margin-top: 10px;
    }

    #g-review .gr-inner-body p{
        margin-bottom: 1.6em;
        text-align: left;
        font: normal normal normal 14px/19px Roboto;
        letter-spacing: 0px;
        color: #00000099;
        line-height: 1.6;
    }


    /**
    ------------------------------------Responsive------------------------------------------------------------
     */

    @media screen and (max-width: 1024px){
        #g-review{
            grid-template-columns: 1fr 1fr;
        }
    }

    @media screen and (max-width: 768px){
        #g-review{
            grid-template-columns: 1fr;
        }
    }
</style>
