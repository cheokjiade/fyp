package com.jiade.fyp.sensingclient.receivers;

import com.jiade.fyp.sensingclient.services.ActivityRecognitionService;
import com.jiade.fyp.sensingclient.services.SensingService;

import android.content.BroadcastReceiver;
import android.content.Context;
import android.content.Intent;

public class BootReceiver extends BroadcastReceiver{

	@Override
	public void onReceive(Context context, Intent intent) {
		Intent startServiceIntent = new Intent(context,SensingService.class);
		context.startService(startServiceIntent);
		Intent startActivityRecognitionIntent = new Intent(context,ActivityRecognitionService.class);
		context.startService(startActivityRecognitionIntent);
		
	}

}
