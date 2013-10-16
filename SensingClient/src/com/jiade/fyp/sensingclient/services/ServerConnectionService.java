package com.jiade.fyp.sensingclient.services;

import java.text.DateFormat;
import java.text.ParseException;
import java.text.SimpleDateFormat;
import java.util.ArrayList;
import java.util.Calendar;
import java.util.Date;
import java.util.List;
import java.util.Timer;
import java.util.TimerTask;

import org.apache.http.NameValuePair;
import org.apache.http.message.BasicNameValuePair;

import com.db4o.ObjectContainer;
import com.db4o.query.Predicate;
import com.google.gson.Gson;
import com.jiade.fyp.sensingclient.db.Db4oHelper;
import com.jiade.fyp.sensingclient.entities.SensingClientJSONContainer;
import com.jiade.fyp.sensingclient.entities.Slocation;
import com.jiade.fyp.sensingclient.settings.SensingSettings;
import com.jiade.fyp.sensingclient.util.HTTPHandler;
import com.jiade.fyp.sensingclient.util.HTTPHandler.OnResponseReceivedListener;

import android.app.Service;
import android.content.Context;
import android.content.Intent;
import android.content.SharedPreferences;
import android.net.ConnectivityManager;
import android.net.NetworkInfo;
import android.os.IBinder;
import android.util.Log;

public class ServerConnectionService extends Service {

	private TimerTask task;
	private Timer timer;
	private SharedPreferences prefs;
	private HTTPHandler httpHandler;
	private ObjectContainer db;
	private DateFormat formatter;
	
	@Override
	public IBinder onBind(Intent intent) {
		return null;
	}

	/* (non-Javadoc)
	 * @see android.app.Service#onCreate()
	 */
	@Override
	public void onCreate() {
		super.onCreate();
		db = Db4oHelper.getInstance(getApplicationContext()).db();
		prefs = this.getSharedPreferences(SensingSettings.PREFS, Context.MODE_PRIVATE);
		formatter = new SimpleDateFormat("dd MMM yyyy HH:mm:ss");
		httpHandler = new HTTPHandler();
		httpHandler.setOnResponseReceivedListener(new OnResponseReceivedListener() {
			@Override
			public void onResponseReceived(String receivedString, boolean success) {
				Log.e("ServerConnectionService.httphandler", receivedString);
				try {
					final Date date = formatter.parse(receivedString);
					Calendar calender = Calendar.getInstance();
		            calender.setTimeInMillis(date.getTime());
		            calender.add(Calendar.SECOND, 1);
		            final Date changeDate=calender.getTime();
					ArrayList <Slocation> locToDel = new ArrayList<Slocation>(Db4oHelper.getInstance(getApplicationContext()).db().query(new Predicate<Slocation>() {
					    public boolean match(Slocation location) {
					        return location.getLocationTimeStamp().compareTo(changeDate)<=0;
					    }
					}));
					for(Slocation tempSLoc : locToDel){
						Db4oHelper.getInstance(getApplicationContext()).db().delete(tempSLoc);
					}
					Db4oHelper.getInstance(getApplicationContext()).db().commit();
					Db4oHelper.getInstance(getApplicationContext()).db().close();
				} catch (ParseException e) {
					e.printStackTrace();
				}
			}
		});
	}

	/* (non-Javadoc)
	 * @see android.app.Service#onStartCommand(android.content.Intent, int, int)
	 */
	@Override
	public int onStartCommand(Intent intent, int flags, int startId) {
		task = new TimerTask() {
            public void run() {
                if (haveNetworkConnection()==2) {
                	sendLocations();
                } else if(haveNetworkConnection()==1){
                	sendLocations();
                }
            }
        };
        timer = new Timer();
        timer.schedule(task, 1*60*1000, 20*60*1000);
		return START_STICKY;
	}
	
	private int haveNetworkConnection() {
	    boolean haveConnectedWifi = false;
	    boolean haveConnectedMobile = false;
	    ConnectivityManager cm = (ConnectivityManager) getSystemService(Context.CONNECTIVITY_SERVICE);
	    NetworkInfo[] netInfo = cm.getAllNetworkInfo();
	    for (NetworkInfo ni : netInfo) {
	        if (ni.getTypeName().equalsIgnoreCase("WIFI"))
	            if (ni.isConnected())
	                haveConnectedWifi = true;
	        if (ni.getTypeName().equalsIgnoreCase("MOBILE"))
	            if (ni.isConnected())
	                haveConnectedMobile = true;
	    }
	    if( haveConnectedWifi) return 2;
	    if (haveConnectedMobile)return 1;
	    else return 0;
	}
	
	private void sendLocations(){
		try{
		if(prefs.getString(SensingSettings.SESSION_HASH, null)!=null){
			SensingClientJSONContainer s = new SensingClientJSONContainer(new ArrayList<Slocation>(Db4oHelper.getInstance(getApplicationContext()).db().query(Slocation.class)), prefs.getString(SensingSettings.SESSION_HASH, null));
			db.close();
			Gson gson = new Gson();
			List<NameValuePair> nameValuePairs = new ArrayList<NameValuePair>();
			nameValuePairs.add(new BasicNameValuePair("location", gson.toJson(s)));
			httpHandler.handleHTTP(nameValuePairs, SensingSettings.DOMAIN + "services/location.php");
		}
		}catch(Exception e){
			e.printStackTrace();
		}
		
	}

}
