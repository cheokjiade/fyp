package com.jiade.fyp.sensingclient.receivers;

import com.jiade.fyp.sensingclient.services.SaveToDBService;

import android.content.BroadcastReceiver;
import android.content.Context;
import android.content.Intent;

public class ScreenReceiver extends BroadcastReceiver {

	public static boolean wasScreenOn = true;
	int state = 0;
	@Override
	public void onReceive(Context context, Intent intent) {
		if (intent.getAction().equals(Intent.ACTION_SCREEN_OFF)) {
            state = SaveToDBService.TURNING_OFF;
            wasScreenOn = false;
        } else if (intent.getAction().equals(Intent.ACTION_SCREEN_ON)) {
        	state = SaveToDBService.TURNING_ON;
            wasScreenOn = true;
        }
		Intent i = new Intent(context, SaveToDBService.class);
		i.putExtra("actionType", SaveToDBService.SCREEN_ON_OFF);
        i.putExtra("screen_state", state);
        context.startService(i);
	}
	

}
