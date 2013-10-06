/**
 * 
 */
package com.jiade.fyp.sensingclient.receivers;

import java.util.Date;

import com.jiade.fyp.sensingclient.db.Db4oHelper;
import com.jiade.fyp.sensingclient.entities.SSMS;
import com.jiade.fyp.sensingclient.entities.Slocation;
import com.jiade.fyp.sensingclient.services.SensingService;

import android.content.BroadcastReceiver;
import android.content.Context;
import android.content.Intent;
import android.location.Location;
import android.os.Bundle;
import android.telephony.SmsMessage;
import android.telephony.TelephonyManager;
import android.util.Log;
import android.widget.Toast;

/**
 * @author Me
 *
 */
public class IncommingSMSReceiver extends BroadcastReceiver {

	@Override
	public void onReceive(Context context, Intent intent) {
		Log.w("IncommingSMSReceiver", "SMS Received");
		Bundle extras = intent.getExtras();
        Object[] pdus = (Object[]) extras.get("pdus");
        TelephonyManager tm = (TelephonyManager) context.getSystemService(Context.TELEPHONY_SERVICE);
        SmsMessage sms;
 
        for (Object pdu : pdus) {
            sms = SmsMessage.createFromPdu((byte[]) pdu);
            Location lastKnownLocation = SensingService.lastKnownLocation;
            
            Slocation loc = new Slocation(Double.toString(lastKnownLocation.getLatitude()), Double.toString(lastKnownLocation.getLongitude()), Double.toString(lastKnownLocation.getAltitude()), lastKnownLocation.getAccuracy(), new Date());
            loc.setObjSMS(new SSMS(SSMS.INCOMMING, sms.getOriginatingAddress(),isADV(sms.getMessageBody()),sms.getMessageBody().length()));
            Db4oHelper.getInstance(context.getApplicationContext()).db().store(loc);
            Db4oHelper.getInstance(context.getApplicationContext()).db().close();
            Toast.makeText(context, "SMS Received and Stored", Toast.LENGTH_SHORT).show();
            Log.w("IncommingSMSReceiver", "SMS Stored");
//            Log.d("Test", "originating number: " + sms.getOriginatingAddress());
//            Log.d("Test", "time received: " + System.currentTimeMillis());
//            Log.d("Test", "number of characters: " + sms.getMessageBody().length());
//            Log.d("Test", "roaming: " + tm.isNetworkRoaming());
        }
	}
	
	private int isADV(String msg){
		if(msg.toLowerCase().startsWith("<adv>"))return 1;
		else return 0;
	}

}
