package com.jiade.fyp.sensingclient.activities;

import java.util.ArrayList;
import java.util.List;

import org.apache.http.NameValuePair;
import org.apache.http.message.BasicNameValuePair;

import com.jiade.fyp.sensingclient.R;
import com.jiade.fyp.sensingclient.db.LocationDAO;
import com.jiade.fyp.sensingclient.json.LocationToJSONConverter;
import com.jiade.fyp.sensingclient.services.SensingService;
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
import android.view.Menu;
import android.view.View;
import android.view.View.OnClickListener;
import android.widget.Button;
import android.widget.EditText;
import android.widget.TextView;

public class MainActivity extends Activity {

	@Override
	protected void onResume() {
		// TODO Auto-generated method stub
		super.onResume();
	}



	String sessionHash;
	String userEmail;
	
	SharedPreferences prefs;
	HTTPHandler httpHandler;
	Handler handler;
	Dialog dSignUp,dLogin;
	ProgressDialog pd;
	TextView tvUsername,tvDetails;
	Button bnRegister, bnLogin;
	
	@Override
	protected void onCreate(Bundle savedInstanceState) {
		super.onCreate(savedInstanceState);
		setContentView(R.layout.activity_main);
		prefs = this.getSharedPreferences(SensingSettings.PREFS, Context.MODE_PRIVATE);
		tvUsername = (TextView)findViewById(R.id.main_info_tv);
		Intent startServiceIntent = new Intent(this,SensingService.class);
		this.startService(startServiceIntent);
		if(!loadPreferences()){
			bnRegister = (Button)findViewById(R.id.main_signup_bn);
			bnLogin = (Button)findViewById(R.id.main_login_bn);
			tvDetails = (TextView)findViewById(R.id.main_detailedinfo_tv);
			if (Build.VERSION.SDK_INT >= Build.VERSION_CODES.HONEYCOMB) {
				tvDetails.setTextIsSelectable(true);
		    }
			bnRegister.setVisibility(View.VISIBLE);
			bnLogin.setVisibility(View.VISIBLE);
			handler = new Handler();
			bnRegister.setOnClickListener(new OnClickListener() {
				
				@Override
				public void onClick(View v) {
					handler.post(new Runnable() {
						
						@Override
						public void run() {
							LocationDAO dao = new LocationDAO(getApplicationContext());
							dao.open();
							tvDetails.setText(LocationToJSONConverter.sensingLocationListToJSONString(dao.getAllSensingLocations()));
							dao.close();
							
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
