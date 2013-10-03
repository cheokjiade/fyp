package com.jiade.fyp.sensingclient.services;

import java.util.Date;

import com.google.android.gms.common.ConnectionResult;
import com.google.android.gms.common.GooglePlayServicesClient;
import com.google.android.gms.common.GooglePlayServicesUtil;
import com.google.android.gms.location.LocationClient;
import com.google.android.gms.location.LocationListener;
import com.google.android.gms.location.LocationRequest;
import com.jiade.fyp.sensingclient.db.Db4oHelper;
import com.jiade.fyp.sensingclient.db.LocationDAO;
import com.jiade.fyp.sensingclient.entities.SensingLocation;
import com.jiade.fyp.sensingclient.entities.Slocation;
import com.nullwire.trace.ExceptionHandler;

import android.app.Service;
import android.content.Intent;
import android.location.Location;
import android.os.Bundle;
import android.os.IBinder;
import android.text.format.Time;
import android.util.Log;
import android.widget.Toast;

public class SensingService extends Service implements
GooglePlayServicesClient.ConnectionCallbacks,
GooglePlayServicesClient.OnConnectionFailedListener,
LocationListener{
	public static Location lastKnownLocation=null;
	public static SensingService ss;
	LocationClient mLocationClient;
	LocationRequest mLocationRequest;
	//LocationDAO dao;
	private int updateInterval;
	@Override
	public void onCreate() {
		updateInterval = 15000;
		ExceptionHandler.register(this.getApplicationContext(), "http://fyp.cheok.org/stacktrace/server.php");
		ss = this;
		if(mLocationClient==null && isSupported()){
			mLocationClient = new LocationClient(this, this, this);
			mLocationClient.connect();
			
		}
		//super.onCreate();
		
		//GooglePlayServicesUtil.isGooglePlayServicesAvailable(this);
	}

	public SensingService() {
	}

	@Override
	public int onStartCommand(Intent intent, int flags, int startId) {
		if(mLocationClient==null && isSupported()){
			mLocationClient = new LocationClient(this, this, this);
			mLocationClient.connect();
			//mLocationClient.requestLocationUpdates(mLocationRequest, this);
		}
		//connect();
		
		return START_STICKY;
	}

	@Override
	public IBinder onBind(Intent intent) {
		// TODO: Return the communication channel to the service.
		throw new UnsupportedOperationException("Not yet implemented");
	}
	
	public boolean isSupported(){
		int resultCode = GooglePlayServicesUtil.isGooglePlayServicesAvailable(this);
		if(resultCode == ConnectionResult.SUCCESS){
			Log.d("Location Updates", "Google Play services is available.");
			Toast.makeText(this, "Google Play services is available, Starting Location Monitor", Toast.LENGTH_SHORT).show();
			return true;
		}else{
			Toast.makeText(this, "Google Play services is not available, Unable to start Location Monitor", Toast.LENGTH_SHORT).show();
			return false;
		}
		
	}

	@Override
	public void onLocationChanged(Location arg0) {
		lastKnownLocation = arg0;
		 // Report to the UI that the location was updated
//        String msg = "Lat: " +
//                Double.toString(arg0.getLatitude()) + "\nLng: " +
//                Double.toString(arg0.getLongitude()) + "\nAlt: " +
//                Double.toString(arg0.getAltitude()) + "\nAccuracy" +
//                Float.toString(arg0.getAccuracy()) + "\nSpeed" +
//                Float.toString(arg0.getSpeed()) + "\nTime" +
//                Long.toString(arg0.getTime());
//        SensingLocation sl = new SensingLocation(
//        		-1l, 
//        		arg0.getTime(), 
//        		Double.toString(arg0.getLatitude()), 
//        		Double.toString(arg0.getLongitude()), 
//        		Double.toString(arg0.getAltitude()), 
//        		Float.toString(arg0.getSpeed()), 
//        		Float.toString(arg0.getAccuracy()));
        //dao.open();
        //dao.createSensingLocation(sl);
        //Toast.makeText(this, "Size:" + Long.toString(dao.getRecords()), Toast.LENGTH_SHORT).show();
        //dao.close();
        if(ActivityRecognitionService.getTempActivity() != null){
        	Db4oHelper.getInstance(getApplicationContext()).db().store(new Slocation(Double.toString(arg0.getLatitude()), Double.toString(arg0.getLongitude()), Double.toString(arg0.getAltitude()), arg0.getAccuracy(), new Date(), null, null, ActivityRecognitionService.getTempActivity()));
        }
        else
        	Db4oHelper.getInstance(getApplicationContext()).db().store(new Slocation(Double.toString(arg0.getLatitude()), Double.toString(arg0.getLongitude()), Double.toString(arg0.getAltitude()), arg0.getAccuracy(), new Date()));
        Db4oHelper.getInstance(getApplicationContext()).db().close();
        //if
        Log.w("Location", Double.toString(arg0.getLatitude())+ " " + Double.toString(arg0.getLongitude()));
        //Toast.makeText(this, Double.toString(arg0.getLatitude())+ " " + Double.toString(arg0.getLongitude()), Toast.LENGTH_SHORT).show();
		
	}

	@Override
	public void onConnectionFailed(ConnectionResult arg0) {
		// TODO Auto-generated method stub
		
	}

	@Override
	public void onConnected(Bundle arg0) {
		Toast.makeText(this, "Connected to location services.", Toast.LENGTH_SHORT).show();
//		dao = new LocationDAO(getApplicationContext());
//		mLocationRequest = LocationRequest.create().setPriority(LocationRequest.PRIORITY_HIGH_ACCURACY).setInterval(15000).setFastestInterval(5000);
//		mLocationRequest.setPriority(LocationRequest.PRIORITY_HIGH_ACCURACY);
//		mLocationRequest.setInterval(15000);
//        // Set the fastest update interval to 1 second
//        mLocationRequest.setFastestInterval(5000);
		mLocationClient.requestLocationUpdates(LocationRequest.create().setPriority(LocationRequest.PRIORITY_HIGH_ACCURACY).setInterval(15000).setFastestInterval(5000), this);
		
	}

	@Override
	public void onDisconnected() {
		mLocationClient.connect();
		
	}
	
	public void setInterval(int interval){
		Log.w("LocationInterval", "Setting interval to " + Integer.toString(interval));
		//mLocationRequest.setInterval(interval);
		mLocationClient.removeLocationUpdates(this);
		mLocationClient.requestLocationUpdates(LocationRequest.create().setPriority(LocationRequest.PRIORITY_HIGH_ACCURACY).setInterval(interval).setFastestInterval(interval-5000), this);
	}

	/* (non-Javadoc)
	 * @see android.app.Service#onDestroy()
	 */
	@Override
	public void onDestroy() {
		ss=null;
		super.onDestroy();
	}

	/**
	 * @return the updateInterval
	 */
	public int getUpdateInterval() {
		return updateInterval;
	}

	/**
	 * @param updateInterval the updateInterval to set
	 */
	public void setUpdateInterval(int updateInterval) {
		this.updateInterval = updateInterval;
	}
}
