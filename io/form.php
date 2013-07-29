
<?php
// In PHP versions earlier than 4.1.0, $HTTP_POST_FILES should be used instead
// of $_FILES.
$as=0;

$uploaddir = './uploads/';
$uploadfile = $uploaddir . basename($_FILES['apsPackage']['name']);
echo basename($_FILES['apsPackage']['tmp_name']);

$xmlString = file_get_contents('zip://' . $_FILES['apsPackage']['tmp_name'] . '#APP-META.xml');
$xml = simplexml_load_string($xmlString);
//echo $test;
?>
<html>
<head>
    <script src="jquery-2.0.3.min.js"></script>
    <script src="jquery.editable-1.3.3.min.js"></script>
    <title>Mined Data</title>
</head>
<body>
<script type="text/javascript">
    function begin(){
        //this.append('Click somewhere else to submit');
    }
    $(document).ready(function(){
    $('.editable').editable({onEdit:begin});
    });
</script>
<!--Table for Application details-->
<table>
    <tr><th colspan="2">App Info</th> </tr>
    <tr>
        <td>Application: </td>
        <td><?php print $xml->id ?></td>
    </tr>
    <tr>
        <td rowspan="2">Packager: </td>
        <td><?php print $xml->packager->name ?></td>
    </tr>
    <tr>
        <td><?php print $xml->packager->uri ?></td>
    </tr>
    <tr>
        <td>Category: </td>
        <td><?php foreach($xml->presentation->categories->category as $category){
                print $category. "<br>";
            } ?></td>
    </tr>
    <tr>
        <td>Hosting Type: </td>
        <td><p class="editable"></p></td>
    </tr>
    <tr>
        <td>Provisioning Type: </td>
        <td><p class="editable"></p></td>
    </tr>
    <tr>
        <td>Business Model: </td>
        <td></td>
    </tr>
</table>
<!-- Table for activation Parameters-->
<table>
    <tr><th colspan="2">Global Application Activation Parameters</th> </tr>
    <tr>
        <td>Auto Provision: </td>
        <td class="editable">Yes/No</td>
    </tr>
    <?php
    $tmpGlblSettings = new SimpleXMLElement($xml->{'global-settings'}->asXML());
    foreach($tmpGlblSettings->xpath("//setting") as $tmp){ ?>
    <tr>
        <td>[<?php print $tmp->attributes()->id ?>]<?php print $tmp->name ?>: </td>
        <td class="editable"><?php print $tmp->attributes()->{'default-value'} ?></td>
    </tr>
    <?php } ?>
</table>

<h2>Application Resources</h2>
<!--application -->
<?php  foreach($xml->service as $tmp){
$as++;
?>
<script  type="text/javascript">
    $(document).ready(function(){
        $("#bs<?php print $as ?>").click(function(){
            $(".as<?php print $as?>:first").clone(true).appendTo(".ds<?php print $as?>");
        });
    });
</script>
<div class="ds<?php print $as?>">
    <button id="bs<?php print $as?>">Clone</button>
    <table class="as<?php print $as?>">
        <tr><th colspan="2">Application</th></tr>

        <tr>
            <td><strong>[Application]<?php print $tmp->attributes()->id ?>: </strong></td>
            <td class="editable resourceName">[Application]Enter Value<?php
                $abc = new SimpleXMLElement($tmp->asXML());
                //$xyz = $abc->xpath("//setting");
                //print_r($tmp) ;
                //echo $tmp->asXML();
                //echo $xyz[0];
                //print_r($xyz); ?></td>
        </tr>
        <?php
        $tmpSimpleXML = $abc->xpath("//setting[@visibility='hidden' and not(@generate) and not(@value-of-setting) and not(self::service)]");
        foreach($tmpSimpleXML as $tmpLv2){ ?>
            <tr>
                <td>[<?php print $tmpLv2['id'] ?>]<?php print $tmpLv2->name ?>: </td>
                <td class="editable"><?php print $tmpLv2->attributes()->{'default-value'} ?></td>
            </tr>
        <?php } ?>
    </table>
</div>
<?php } ?>

<!-- application services-->
<?php
    $tmpAppSvc = new SimpleXMLElement($xml->{'service'}->asXML());
    foreach($tmpAppSvc->xpath(".//service") as $tmp){
    $as++;
    ?>
<script  type="text/javascript">
    $(document).ready(function(){
        $("#bs<?php print $as ?>").click(function(){
            $(".as<?php print $as?>:first").clone(true).appendTo(".ds<?php print $as?>");
        });
    });
</script>
<div class="ds<?php print $as?>">
    <button id="bs<?php print $as?>">Clone</button>
<table class="as<?php print $as?>">
    <tr><th colspan="2">Service Resources</th></tr>

        <tr>
            <td><strong>[Service]<?php print $tmp->attributes()->id ?>: </strong></td>
            <td class="editable resourceName">[Service]Enter Value<?php
                $abc = new SimpleXMLElement($tmp->asXML());
                //$xyz = $abc->xpath("//setting");
                //print_r($tmp) ;
                //echo $tmp->asXML();
                //echo $xyz[0];
                //print_r($xyz); ?></td>
        </tr>
        <?php
        $tmpSimpleXML = $abc->xpath("//setting[@visibility='hidden' and not(@generate) and not(@value-of-setting) and not(self::service)]");
        foreach($tmpSimpleXML as $tmpLv2){ ?>
            <tr>
                <td>[<?php print $tmpLv2['id'] ?>]<?php print $tmpLv2->name ?>: </td>
                <td class="editable"><?php print $tmpLv2->attributes()->{'default-value'} ?></td>
            </tr>
        <?php } ?>
</table>
</div>
<?php } ?>

<div id="serviceplan">
    <script  type="text/javascript">
        var spArray = [];
        $(document).ready(function(){
            $("#sp1bn").click(function(){
                $("#sp1p1").clone(true).appendTo("#sp1");
            });
        });
    </script>
    <h2>Service Plans</h2>
    <table>
        <tr>
            <td>Plan Name:</td>
            <td class="editable"></td>
        </tr>
        <tr>
            <td colspan="2"><strong>Service Terms</strong></td>
        </tr>
        <tr>
            <td colspan="2">
                <table>
                    <tr>
                        <td>Grace Period: </td>
                        <td class="editable" style="width:150px">Days</td>
                        <td>Hold Period: </td>
                        <td class="editable" style="width:150px">Days</td>
                        <td>Destroy After Hold Period</td>
                        <td class="editable">yes/no</td>
                    </tr>
                </table>
            </td>
        </tr>
        <!--<tr>
            <td>Grace Period: </td>
            <td class="editable">Days</td>
            <td>Hold Period: </td>
            <td>Days</td>
            <td>Destroy After Hold Period</td>
            <td>yes/no</td>
        </tr>
        <tr>
            <td>Hold Period: </td>
            <td>Days</td>
        </tr>
        <tr>
            <td>Grace Period: </td>
            <td>Days</td>
        </tr>-->
        <tr>
            <td>Short Description:</td>
            <td class="editable"></td>
        </tr>
        <tr>
            <td>Long Description:</td>
            <td class="editable"></td>
        </tr>
        <tr>
            <td>Unique Group:</td>
            <td class="editable"></td>
        </tr>
        <tr>
            <td>Welcome Email Template Name:</td>
            <td class="editable"></td>
        </tr>
        <tr>
            <td>Customer Class:</td>
            <td class="editable"></td>
        </tr>
        <tr>
            <td>Billing Type:</td>
            <td class="editable"></td>
        </tr>
        <tr>
            <td>Subscription Periods <button id="sp1bn">Add Period</button></td>
        </tr>
        <tr>
            <td>
                <table id="sp1">
                    <tr>
                        <td>Period</td>
                        <td>Setup Fee</td>
                        <td>Renewal Fee</td>
                        <td>Recurring Fee</td>
                        <td>Refund Period(days)</td>
                        <td>Non-Refundable Amount</td>
                        <td>Cancel Fee</td>
                        <td>Period Discount</td>
                        <td>Store Pricing Format</td>
                        <td>Trial</td>
                    </tr>
                    <tr id="sp1p1">
                        <td class="editable">x months/years</td>
                        <td class="editable">SGD0.00</td>
                        <td class="editable">SGD0.00</td>
                        <td class="editable">SGD0.00</td>
                        <td class="editable">0 Days</td>
                        <td class="editable">SGD0.00</td>
                        <td class="editable">SGD0.00</td>
                        <td class="editable">10%/SGD10</td>
                        <td class="editable">SGDXX.XX Per Plan Per Month</td>
                        <td class="editable">Yes/No</td>
                    </tr>
                </table>
            </td>
        </tr>
        <tr>
            <th colspan="2">
                <script>
                    $(document).ready(function(){
                        $('#sp1ressel').click(function(){
                            var selectContents = "";
                            $('.resourceName').each(function(i, obj) {
                                selectContents += "\<option\>" + $(this).text() + "\</option\>";
                            });
                            $('#sp1ressel').html(selectContents);
                        });
                        $("#sp1addbn").click(function(){
                            $("#sp1ressel:selected").each(function () {
                                str += $(this).text() + " ";
                            });
                        });
                    });
                </script>
                Resources
                <select id="sp1ressel"></select><button id="sp1addbn">Add</button>
            </th>
        </tr>
        <tr>
            <td colspan="2">
                <table id="sp1rst">
                    <tr id="sp1rs1">
                        <td>Resource Name</td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

</div>
</body>
</html>


<?php

echo '<pre>';
if (move_uploaded_file($_FILES['apsPackage']['tmp_name'], $uploadfile)) {
    echo "File is valid, and was successfully uploaded.\n";
} else {
    echo "Possible file upload attack!\n";
}

echo 'Here is some more debugging info:';
print_r($_FILES);

print "</pre>";

?>
