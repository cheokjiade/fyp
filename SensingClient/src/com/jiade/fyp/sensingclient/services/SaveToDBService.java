package com.jiade.fyp.sensingclient.services;

import java.util.Date;

import com.db4o.ObjectContainer;
import com.jiade.fyp.sensingclient.db.Db4oHelper;
import com.jiade.fyp.sensingclient.entities.SAction;
import com.jiade.fyp.sensingclient.entities.SSMS;
import com.jiade.fyp.sensingclient.entities.Slocation;
import com.jiade.fyp.sensingclient.receivers.ScreenReceiver;

import android.app.IntentService;
import android.app.Service;
import android.content.BroadcastReceiver;
import android.content.Intent;
import android.content.IntentFilter;
import android.location.Location;
import android.os.IBinder;

public class SaveToDBService extends IntentService {

	
	public static final int SCREEN_ON_OFF = 1, SMS_RECEIVED = 3;
	public static final int TURNING_ON = 0, TURNING_OFF = 1;
	public SaveToDBService() {
		super("SaveToDBService");
	}

	private ObjectContainer db;
	
	@Override
	public IBinder onBind(Intent arg0) {
		return null;
	}

	/* (non-Javadoc)
	 * @see android.app.Service#onCreate()
	 */
	@Override
	public void onCreate() {
		// TODO Auto-generated method stub
		super.onCreate();
		db = Db4oHelper.getInstance(getApplicationContext()).db();
		
		
	}

	@Override
	protected void onHandleIntent(Intent intent) {
		int action = intent.getIntExtra("actionType", 0);
		if(action==0)return;
		Location lastKnownLocation = SensingService.lastKnownLocation;
        Slocation loc = new Slocation(Double.toString(lastKnownLocation.getLatitude()), Double.toString(lastKnownLocation.getLongitude()), Double.toString(lastKnownLocation.getAltitude()), lastKnownLocation.getAccuracy(), new Date());
		if(action==SMS_RECEIVED){
            loc.setObjSMS(new SSMS(SSMS.INCOMMING, intent.getStringExtra("ORIGINATING_ADDRESS"),intent.getIntExtra("IS_ADV", 0),intent.getIntExtra("MSG_LENGTH",1)));
		}else if(action==SCREEN_ON_OFF){
			loc.setObjAction(new SAction(SCREEN_ON_OFF, intent.getIntExtra("screen_state", 0)));
		}
		db.store(loc);
        db.close();
	}

	
}
