package com.jiade.fyp.sensingclient.util;

import android.os.Build;

public class SensingDevice {
	
	public static String deviceInformation(){
		StringBuffer buf = new StringBuffer();
		buf.append("VERSION.RELEASE {"+Build.VERSION.RELEASE+"}");
		buf.append("\nVERSION.INCREMENTAL {"+Build.VERSION.INCREMENTAL+"}");
		buf.append("\nVERSION.SDK_INT {"+Build.VERSION.SDK_INT+"}");
		buf.append("\nFINGERPRINT {"+Build.FINGERPRINT+"}");
		buf.append("\nBOARD {"+Build.BOARD+"}");
		buf.append("\nBRAND {"+Build.BRAND+"}");
		buf.append("\nDEVICE {"+Build.DEVICE+"}");
		buf.append("\nMANUFACTURER {"+Build.MANUFACTURER+"}");
		buf.append("\nMODEL {"+Build.MODEL+"}");
		return buf.toString();
	}

}
