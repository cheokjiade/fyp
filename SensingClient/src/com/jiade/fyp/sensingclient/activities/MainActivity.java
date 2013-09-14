package com.jiade.fyp.sensingclient.activities;

import java.util.ArrayList;
import java.util.Date;
import java.util.List;

import org.apache.http.NameValuePair;
import org.apache.http.message.BasicNameValuePair;

import com.db4o.ObjectContainer;
import com.db4o.query.Predicate;
import com.google.gson.Gson;
import com.jiade.fyp.sensingclient.R;
import com.jiade.fyp.sensingclient.db.Db4oHelper;
import com.jiade.fyp.sensingclient.db.LocationDAO;
import com.jiade.fyp.sensingclient.entities.SSMS;
import com.jiade.fyp.sensingclient.entities.SensingClientJSONContainer;
import com.jiade.fyp.sensingclient.entities.Slocation;
import com.jiade.fyp.sensingclient.json.LocationToJSONConverter;
import com.jiade.fyp.sensingclient.services.ActivityRecognitionService;
import com.jiade.fyp.sensingclient.services.SensingService;
import com.jiade.fyp.sensingclient.services.ServerConnectionService;
import com.jiade.fyp.sensingclient.settings.SensingSettings;
import com.jiade.fyp.sensingclient.util.DeviceUuidFactory;
import com.jiade.fyp.sensingclient.util.HTTPHandler;
import com.jiade.fyp.sensingclient.util.HTTPHandler.OnResponseReceivedListener;
import com.jiade.fyp.sensingclient.util.SensingDevice;

import android.os.Build;
import android.os.Bundle;
import android.os.Handler;
import android.os.UserHandle;
import android.app.Activity;
import android.app.Dialog;
import android.app.ProgressDialog;
import android.content.Context;
import android.content.DialogInterface;
import android.content.Intent;
import android.content.DialogInterface.OnDismissListener;
import android.content.SharedPreferences;
import android.content.SharedPreferences.Editor;
import android.content.pm.ResolveInfo;
import android.util.Log;
import android.view.Menu;
import android.view.View;
import android.view.View.OnClickListener;
import android.widget.Button;
import android.widget.EditText;
import android.widget.TextView;
import android.widget.Toast;

public class MainActivity extends Activity {

	@Override
	protected void onResume() {
		// TODO Auto-generated method stub
		super.onResume();
	}



	String sessionHash;
	String userEmail;
	
	SharedPreferences prefs;
	HTTPHandler httpHandler, sampleHandler;
	Handler handler;
	Dialog dSignUp,dLogin;
	ProgressDialog pd;
	TextView tvUsername,tvDetails;
	Button bnRegister, bnLogin;
	ArrayList<Slocation> arrayListLocation;
	
	@Override
	protected void onCreate(Bundle savedInstanceState) {
		super.onCreate(savedInstanceState);
		setContentView(R.layout.activity_main);
		Log.e("Log", "Start");
		prefs = this.getSharedPreferences(SensingSettings.PREFS, Context.MODE_PRIVATE);
		tvUsername = (TextView)findViewById(R.id.main_info_tv);
		ObjectContainer db = Db4oHelper.getInstance(getApplicationContext()).db();
		final List<Slocation> locationList = db.query(Slocation.class);
		arrayListLocation = new ArrayList<Slocation>(locationList);
		final Date currentDate = new Date(System.currentTimeMillis() - 60 * 1000);
//		final List<Slocation> testLocations = db.query(new Predicate<Slocation>() {
//		    public boolean match(Slocation location) {
//		        return location.getLocationTimeStamp().after(currentDate);
//		    }
//		});
//		ArrayList <Slocation> locToDel = new ArrayList<Slocation>(db.query(new Predicate<Slocation>() {
//		    public boolean match(Slocation location) {
//		        return location.getLocationTimeStamp().before(currentDate);
//		    }
//		}));
//		for(Slocation tempSLoc : locToDel){
//			db.delete(tempSLoc);
//		}
//		db.commit();
		//arrayListLocation = new ArrayList<Slocation>(testLocations);
		//ArrayList<SSMS> smsList = new ArrayList<SSMS>(db.query(SSMS.class));
		//db.store(new Slocation("lat", "lng", "alt", 20.0f, new Date()));
		//db.store(new Slocation("lat1", "lng1", "alt1", 20.0f, new Date()));
		
		Intent intent = new Intent("android.provider.Telephony.SMS_RECEIVED");
		List<ResolveInfo> infos = getPackageManager().queryBroadcastReceivers(intent, 0);
		for (ResolveInfo info : infos) {
		    Log.w("MainActivity","Receiver name:" + info.activityInfo.name + "; priority=" + info.priority);
		}
		Intent startServiceIntent = new Intent(this,SensingService.class);
		this.startService(startServiceIntent);
		Intent startActivityRecognitionIntent = new Intent(this,ActivityRecognitionService.class);
		this.startService(startActivityRecognitionIntent);
		Intent startServerConnectionIntent = new Intent(this,ServerConnectionService.class);
		this.startService(startServerConnectionIntent);
		tvDetails = (TextView)findViewById(R.id.main_detailedinfo_tv);
		if (Build.VERSION.SDK_INT >= Build.VERSION_CODES.HONEYCOMB) {
			tvDetails.setTextIsSelectable(true);
	    }
		if(prefs.getString(SensingSettings.SESSION_HASH, null)!=null){
			SensingClientJSONContainer s = new SensingClientJSONContainer(new ArrayList<Slocation>(db.query(Slocation.class)), prefs.getString(SensingSettings.SESSION_HASH, null));
			Gson gson = new Gson();
			tvDetails.setText(gson.toJson(s));
			
		}
		db.close();
//		sampleHandler = new HTTPHandler();
//		sampleHandler.setOnResponseReceivedListener(new OnResponseReceivedListener() {
//			
//			@Override
//			public void onResponseReceived(String receivedString, boolean success) {
//				Toast.makeText(getApplicationContext(), receivedString, Toast.LENGTH_SHORT).show();
//				
//			}
//		});
//		sampleHandler.handleHTTP(null, "http://maps.googleapis.com/maps/api/directions/json?mode=walking&origin=1.339287%2C103.706383&destination=1.338215%2C103.697223&sensor=true");
		if(!loadPreferences()){
			bnRegister = (Button)findViewById(R.id.main_signup_bn);
			bnLogin = (Button)findViewById(R.id.main_login_bn);
			
			
			bnRegister.setVisibility(View.VISIBLE);
			bnLogin.setVisibility(View.VISIBLE);
			handler = new Handler();
			bnRegister.setOnClickListener(new OnClickListener() {
				
				@Override
				public void onClick(View v) {
					handler.post(new Runnable() {
						
						@Override
						public void run() {
							//LocationDAO dao = new LocationDAO(getApplicationContext());
							//dao.open();
							//tvDetails.setText(LocationToJSONConverter.sensingLocationListToJSONString(dao.getAllSensingLocations()));
							//dao.close();
							Gson gson = new Gson();
							tvDetails.setText(gson.toJson(arrayListLocation));
							
						}
					});
					
				}
			});
			
			bnLogin.setOnClickListener(new OnClickListener() {
				
				@Override
				public void onClick(View v) {
					showLogin();
				}
			});
		}else{
			tvUsername.setText("Welcome " + userEmail);
		}
	}
	
	private void showLogin(){
		dLogin = new Dialog(this);
		dLogin.setContentView(R.layout.login_layout);
		dLogin.setTitle("Login");
		dLogin.setOnDismissListener(new OnDismissListener() {

			@Override
			public void onDismiss(DialogInterface dialog) {
				dLogin = null;
			}
		});
		
		handler = new Handler();
		httpHandler = new HTTPHandler();
		
		final EditText etEmail = (EditText) dLogin.findViewById(R.id.login_email_et);
		final EditText etPassword = (EditText) dLogin.findViewById(R.id.login_password_et);
		final TextView tvMsg = (TextView)dLogin.findViewById(R.id.login_msg_tv);
		Button bnSubmit = (Button)dLogin.findViewById(R.id.login_submit_bn);
		
		bnSubmit.setOnClickListener(new OnClickListener() {
			
			@Override
			public void onClick(View v) {
				DeviceUuidFactory uuid = new DeviceUuidFactory(getApplicationContext());
				List<NameValuePair> nameValuePairs = new ArrayList<NameValuePair>();
				nameValuePairs.add(new BasicNameValuePair("action", "connect"));
				nameValuePairs.add(new BasicNameValuePair("email", etEmail.getEditableText().toString()));
				nameValuePairs.add(new BasicNameValuePair("pw", etPassword.getEditableText().toString()));
				nameValuePairs.add(new BasicNameValuePair("deviceid", uuid.getDeviceUuid().toString()));
				nameValuePairs.add(new BasicNameValuePair("details", SensingDevice.deviceInformation()));
				
				httpHandler.setOnResponseReceivedListener(new OnResponseReceivedListener() {
					
					@Override
					public void onResponseReceived(final String receivedString, boolean success) {
						if(receivedString.startsWith("Success")){
							savePreference(SensingSettings.SESSION_HASH, receivedString.split(" ")[1]);
							savePreference(SensingSettings.USER_EMAIL, etEmail.getEditableText().toString());
//							prefs.edit().putString(SensingSettings.SESSION_HASH, receivedString.split(" ")[1]);
//							prefs.edit().commit();
//							prefs.edit().putString(SensingSettings.USER_EMAIL, etEmail.getEditableText().toString());
//							prefs.edit().commit();
							userEmail = etEmail.getEditableText().toString();
							sessionHash = receivedString.split(" ")[1];
							handler.post(new Runnable() {
								
								@Override
								public void run() {
									tvUsername.setText("Welcome " + userEmail);
									
								}
							});
							
							
							dLogin.dismiss();
							dLogin=null;
						}
						else{
							handler.post(new Runnable() {
								
								@Override
								public void run() {
									// TODO Auto-generated method stub
									tvMsg.setText(receivedString);
									tvMsg.setVisibility(View.VISIBLE);
								}
							});
							
						}
						
						
					}
				});
				httpHandler.handleHTTP(nameValuePairs, SensingSettings.DOMAIN + "services/device.php");
				
			}
		});
		dLogin.setCancelable(true);
		dLogin.show();
	}
	
	private boolean loadPreferences(){	 
		 userEmail = prefs.getString(SensingSettings.USER_EMAIL, null);
		 sessionHash = prefs.getString(SensingSettings.SESSION_HASH, null);
		 if(userEmail==null || sessionHash == null)
			 return false;
		 return true;
	}
	
	private void savePreference(String key, String value){
		Editor e = prefs.edit();
		e.putString(key, value);
		e.commit();
	}
	
	

	@Override
	public boolean onCreateOptionsMenu(Menu menu) {
		// Inflate the menu; this adds items to the action bar if it is present.
		getMenuInflater().inflate(R.menu.main, menu);
		return true;
	}

}
