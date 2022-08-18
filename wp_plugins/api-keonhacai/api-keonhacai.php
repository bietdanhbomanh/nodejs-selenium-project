<?php

/**
 * Plugin Name: Tylekeo-keonhacai Soccer ST
 * Plugin URI: tykeo365.xyz/
 * Description: Display data tylekeo-keonhacai using a shortcode to insert in a page or post
 * Version: 1.4
 * Text Domain: tylekeo-keonhacai-soccer-st
 * Author: ST
 * Author URI: tyk365.xyz
 */
?>

<?php
const SOURCE_DATA_API_KEONHACAI = 'http://tylekeo365.xyz';
const SORTCODE_API_KEONHACAI = 'data-api-keonhacai';


function call_data_api_keonhacai($date = 1)
{
    $curl = curl_init();


    $params = array(
        'type' => 'keonhacai',
        'date' => $date,
        'device' => is_mobile_api_keonhacai() ? 'mobile' : 'pc'
    );


    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($curl, CURLOPT_URL, SOURCE_DATA_API_KEONHACAI . '/api?' . http_build_query($params));

    $response = curl_exec($curl);
    return $response;
}



function is_mobile_api_keonhacai()
{
    return preg_match("/(android|avantgo|blackberry|bolt|boost|cricket|docomo|fone|hiptop|mini|mobi|palm|phone|pie|tablet|up\.browser|up\.link|webos|wos)/i", $_SERVER["HTTP_USER_AGENT"]);
}


add_shortcode(SORTCODE_API_KEONHACAI, 'echo_data_api_keonhacai');


function echo_data_api_keonhacai()
{
    $Date = date("Y-m-d");
    function getDateApi($add, $current)
    {
        return date('d/m', strtotime($current . ' + ' . $add . ' days'));
    }
    $data = call_data_api_keonhacai();
    return '
    <div class="wrapper-api-keonhacai">
        <div class="search-form">
        <button class="btn-keo-ngay active" value="1" date="KÈO NHÀ CÁI HÔM NAY">HÔM NAY</button>
        <button class="btn-keo-ngay" value="2" date="KÈO NHÀ CÁI NGÀY MAI">NGÀY MAI</button>
        <button class="btn-keo-ngay" value="3" date="KÈO NHÀ CÁI NGÀY ' . getDateApi(2, $Date) . '">' . getDateApi(2, $Date) . '</button
        ><button class="btn-keo-ngay" value="4" date="KÈO NHÀ CÁI NGÀY ' . getDateApi(3, $Date) . '">' . getDateApi(3, $Date) . '</button
        ><button class="btn-keo-ngay" value="5" date="KÈO NHÀ CÁI NGÀY ' . getDateApi(4, $Date) . '">' . getDateApi(4, $Date) . '</button
        ><button class="btn-keo-ngay" value="6" date="KÈO NHÀ CÁI NGÀY ' . getDateApi(5, $Date) . '">' . getDateApi(5, $Date) . '</button>
        </div>
        ' . $data . '
    </div>
    ';
}



function footer_script_api_keonhacai()
{ ?>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js" integrity="sha512-894YE6QWD5I59HgZOGReFYm4dnWc1Qt5NtvYSaNcOP+u1T9qYdvdihz0PPSiiqn/+/3e7Jo4EaG7TubfWGUrMQ==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/socket.io/4.5.1/socket.io.js" integrity="sha512-9mpsATI0KClwt+xVZfbcf2lJ8IFBAwsubJ6mI3rtULwyM3fBmQFzj0It4tGqxLOGQwGfJdk/G+fANnxfq9/cew==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script>
        let rootApiKeonhacai = document.getElementById("odd-table");
        if (rootApiKeonhacai) {
            const socketKeonhacai = io("<?= SOURCE_DATA_API_KEONHACAI ?>/")
            <?php if (is_mobile_api_keonhacai()) : ?>
                socketKeonhacai.on('keonhacaimobile', function(data) {
                    if ($('.active[value=1]').length > 0) {
                        document.getElementById("odd-table").outerHTML = data.data;
                    }
                });
            <?php else :  ?>
                socketKeonhacai.on('keonhacaipc', function(data) {
                    if ($('.active[value=1]').length > 0) {
                        document.getElementById("odd-table").outerHTML = data.data;
                    }
                });
            <?php endif;  ?>
        }
    </script>

    <script type="text/javascript">
        (function($) {
            $(document).ready(function() {
                $('.btn-keo-ngay').click(function() {
                    if (!$(this).hasClass('active')) {
                        $.ajax({
                            type: "post",
                            dataType: "json",
                            url: '<?php echo admin_url('admin-ajax.php'); ?>', //Đường dẫn chứa hàm xử lý dữ liệu. Mặc định của WP như vậy
                            data: {
                                action: "api_keonhacai", //Tên action
                                date: this.value
                            },
                            context: this,
                            beforeSend: function() {
                                document.getElementById("odd-table").innerHTML = '<div class="loader-ajax"></div>';

                                $('.active.btn-keo-ngay').removeClass('active');
                            },
                            success: function(response) {
                                this.classList.add('active');

                                //Làm gì đó khi dữ liệu đã được xử lý
                                if (response.success) {
                                    document.getElementById("odd-table").outerHTML = response.data;
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

add_action('wp_footer', 'footer_script_api_keonhacai');


function enqueue_scripts_and_styles_api_keonhacai()
{
    wp_register_script('my_plugin-script_api_keonhacai', plugins_url('/js/keonhacai.js', __FILE__));
    wp_enqueue_script('my_plugin-script_api_keonhacai');

    wp_register_style('my_plugin_style_api_keonhacai', plugins_url('/css/keonhacai.css', __FILE__));
    wp_enqueue_style('my_plugin_style_api_keonhacai');
}
add_action('wp_enqueue_scripts', 'enqueue_scripts_and_styles_api_keonhacai');





add_action('wp_ajax_api_keonhacai', 'api_keonhacai_init');
add_action('wp_ajax_nopriv_api_keonhacai', 'api_keonhacai_init');

function api_keonhacai_init()
{

    //do bên js để dạng json nên giá trị trả về dùng phải encode
    $date = (isset($_POST['date'])) ? esc_attr($_POST['date']) : '';
    wp_send_json_success(call_data_api_keonhacai($date));

    die(); //bắt buộc phải có khi kết thúc
}
