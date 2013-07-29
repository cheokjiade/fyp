<?php
/**
 * Created by IntelliJ IDEA.
 * User: Jiade
 * Date: 27/05/13
 * Time: 16:52
 * To change this template use File | Settings | File Templates.
 */
function generateSalt() {
    mt_srand(microtime(true)*100000 + memory_get_usage(true));
    return md5(uniqid(mt_rand(), true));
}