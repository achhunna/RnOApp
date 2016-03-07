<?php
//Session initiator

session_start();

if (isset($_POST["submit"]))
{
    if ($_POST["formId"] == $_SESSION["formId"])
    {
        $_SESSION["formId"] = '';
        echo 'Process form';
    }
    else
        echo 'Don\'t process form';
}
else
{
    $_SESSION["formId"] = md5(rand(0,10000000));
?>
    <form action="<?php echo $_SERVER["PHP_SELF"]; ?>" method="post">
    <input type="hidden" name="formId" value="<?php echo $_SESSION["formId"]; ?>" />
    <input type="submit" name="submit" />
</form>
<?php } ?>