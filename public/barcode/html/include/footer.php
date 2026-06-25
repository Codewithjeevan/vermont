<?php
if (!defined('IN_CB')) { die('You are not allowed to access to this page.'); }
?>

            <div class="output">
                <section class="output">
                    <h3>Output&nbsp;&nbsp;&nbsp;<a href="javascript:Clickheretoprint()" style="color: #000000; text-decoration: none;">Print</a></h3>
                    <?php
                        $finalRequest = '';
                        foreach (getImageKeys() as $key => $value) {
                            $finalRequest .= '&' . $key . '=' . urlencode($value);
                        }
                        if (strlen($finalRequest) > 0) {
                            $finalRequest[0] = '?';
                        }
                    ?>
                    <div id="imageOutput">
						<?php
						$N=$ncode;
						for($i=0; $i < $N; $i++)
						{
						?>
                        <?php if ($imageKeys['text'] !== '') { ?><img style="margin: 0 20px 20px 0;" src="image.php<?php echo $finalRequest; ?>" alt="Barcode Image" /><?php }
                        else { ?>Fill the form to generate a barcode.<?php } }?>
                    </div>
                </section>
            </div>
        </form>

        <div class="footer">
            <footer>
            All Rights Reserved &copy; <?php date_default_timezone_set('UTC'); echo date('Y'); ?> <a href="http://www.barcodephp.com" target="_blank">Barcode Generator</a>
            <br /><?php echo $code; ?> PHP5-v<?php echo $codeVersion; ?>
            </footer>
        </div>
    </body>
</html>