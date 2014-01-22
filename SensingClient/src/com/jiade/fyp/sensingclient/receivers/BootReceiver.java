package com.jiade.fyp.sensingclient.receivers;

import com.jiade.fyp.sensingclient.services.ActivityRecognitionService;
import com.jiade.fyp.sensingclient.services.SaveToDBService;
import com.jiade.fyp.sensingclient.services.SensingService;
import com.jiade.fyp.sensingclient.services.ServerConnectionService;

import android.content.BroadcastReceiver;
import android.content.Context;
import android.content.Intent;
import android.content.IntentFilter;
import android.util.Log;
import android.widget.Toast;

public class BootReceiver extends BroadcastReceiver{

	@Override
	public void onReceive(Context context, Intent intent) {
		Toast.makeText(context, "Booted", Toast.LENGTH_SHORT).show();
		Log.e("SensingClient", "Booted");
		Intent startServiceIntent = new Intent(context,SensingService.class);
		context.startService(startServiceIntent);
		Intent startActivityRecognitionIntent = new Intent(context,ActivityRecognitionService.class);
		context.startService(startActivityRecognitionIntent);
		Intent startServerConnectionIntent = new Intent(context,ServerConnectionService.class);
		context.startService(startServerConnectionIntent);
		Intent saveToDBIntent = new Intent(context,SaveToDBService.class);
		context.startService(saveToDBIntent);
		
	}

}
