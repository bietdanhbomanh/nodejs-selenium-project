<?php

/**
 * Plugin Name: Tylekeo-keocacuoc Soccer ST
 * Plugin URI: tykeo365.xyz/api
 * Description: Display data tylekeo-keocacuoc using a shortcode to insert in a page or post
 * Version: 0.1
 * Text Domain: tylekeo-soccer-st
 * Author: ST
 * Author URI: tyk365.xyz
 */
?>

<?php

const SOURCE_DATA_API_KEOCACUOC = 'http://tylekeo365.xyz';
const SORTCODE_API_KEOCACUOC = 'data-api-keocacuoc';


function call_data_api_keocacuoc($date = 1)
{
    $curl = curl_init();


    $query = array(
        'type' => 'keocacuoc',
        'date' => $date,
        'device' => 'pc',
    );


    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($curl, CURLOPT_URL, SOURCE_DATA_API_KEOCACUOC . '/api?' . http_build_query($query));

    $response = curl_exec($curl);
    return $response;
}
add_shortcode(SORTCODE_API_KEOCACUOC, 'echo_data_api_keocacuoc');





function echo_data_api_keocacuoc()
{

    $Date = date("Y-m-d");
    function getDateApi($add, $current)
    {
        return date('d/m', strtotime($current . ' + ' . $add . ' days'));
    }

    $data = call_data_api_keocacuoc();
    return '
    <div class="wrapper_api_keocacuoc>
        <div style="margin-top: 8px;">
            <div class="c-odds-page__filter">
                <div data-value="1" class="nofil active">Live</div>
                <div data-value="2" class="nofil">Hôm nay</div>
                <div data-value="3" class="nofil">' . getDateApi(1, $Date) . '</div>
                <div data-value="4" class="nofil">' . getDateApi(2, $Date) . '</div>
                <div data-value="5" class="nofil">' . getDateApi(3, $Date) . '</div>
                <div data-value="6" class="nofil">' . getDateApi(4, $Date) . '</div>
                <div data-value="7" class="nofil">' . getDateApi(5, $Date) . '</div>
                <div data-value="8" class="nofil">' . getDateApi(6, $Date) . '</div>
            </div>
        </div>
    ' . $data . '
    </div>
    ';
}



function footer_script_api_keocacuoc()
{ ?>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js" integrity="sha512-894YE6QWD5I59HgZOGReFYm4dnWc1Qt5NtvYSaNcOP+u1T9qYdvdihz0PPSiiqn/+/3e7Jo4EaG7TubfWGUrMQ==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/socket.io/4.5.1/socket.io.js" integrity="sha512-9mpsATI0KClwt+xVZfbcf2lJ8IFBAwsubJ6mI3rtULwyM3fBmQFzj0It4tGqxLOGQwGfJdk/G+fANnxfq9/cew==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script>
        let rootApiKeocacuoc = document.querySelector('[data-view=asiaview]');
        if (rootApiKeocacuoc) {
            const socketKeocacuoc = io("<?= SOURCE_DATA_API_KEOCACUOC ?>/")
            socketKeocacuoc.on('keocacuoc', function(data) {
                if ($('.nofil.active[data-value=1]').length > 0) {
                    document.querySelector('[data-view=asiaview]').outerHTML = data.data;
                }
            });
        }
    </script>

    <script type="text/javascript">
        (function($) {
            $(document).ready(function() {
                $('.c-odds-page__filter .nofil').click(function() {
                    if (!$(this).hasClass('active')) {
                        $.ajax({
                            type: "post",
                            dataType: "json",
                            url: '<?php echo admin_url('admin-ajax.php'); ?>', //Đường dẫn chứa hàm xử lý dữ liệu. Mặc định của WP như vậy
                            data: {
                                action: "api_keocacuoc", //Tên action
                                date: this.getAttribute('data-value'), //
                            },
                            context: this,
                            beforeSend: function() {
                                document.querySelector('[data-view=asiaview]').innerHTML = '<div class="loader-ajax"></div>';
                                $('.active.nofil').removeClass('active');
                            },
                            success: function(response) {
                                this.classList.add('active');

                                //Làm gì đó khi dữ liệu đã được xử lý
                                if (response.success) {
                                    document.querySelector('[data-view=asiaview]').outerHTML = response.data;
                                } else {
                                    alert('Đã có lỗi xảy ra');
                                }
                            },
                            error: function(jqXHR, textStatus, errorThrown) {
                                //Làm gì đó khi có lỗi xảy ra
                                console.log('The following error occured: ' + textStatus, errorThrown);
                            }
                        })
                        return false;
                    }
                })
            })
        })(jQuery)
    </script>

<?php }
add_action('wp_footer', 'footer_script_api_keocacuoc');

function enqueue_scripts_and_styles_api_keocacuoc()
{

    wp_register_script('my_plugin-script_api_keocacuoc', plugins_url('/js/keocacuoc.js', __FILE__));
    wp_enqueue_script('my_plugin-script_api_keocacuoc');


    wp_register_style('my_plugin_style_api_keocacuoc', plugins_url('/css/keocacuoc.css', __FILE__));
    wp_enqueue_style('my_plugin_style_api_keocacuoc');
}
add_action('wp_enqueue_scripts', 'enqueue_scripts_and_styles_api_keocacuoc');




add_action('wp_ajax_api_keocacuoc', 'api_keocacuoc_init');
add_action('wp_ajax_nopriv_api_keocacuoc', 'api_keocacuoc_init');

function api_keocacuoc_init()
{

    //do bên js để dạng json nên giá trị trả về dùng phải encode
    $date = (isset($_POST['date'])) ? esc_attr($_POST['date']) : '';
    wp_send_json_success(call_data_api_keocacuoc($date));

    die(); //bắt buộc phải có khi kết thúc
}
