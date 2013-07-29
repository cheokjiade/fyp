package com.jiade.fyp.sensingclient.db;

import android.content.Context;
import android.database.sqlite.SQLiteDatabase;
import android.database.sqlite.SQLiteDatabase.CursorFactory;
import android.database.sqlite.SQLiteOpenHelper;

public class SQLiteConnector extends SQLiteOpenHelper{

	public static final int DATABASE_VERSION = 1;
	public static final String DATABASE_NAME = "location.db";
	public static final String LOCATION_TABLE = "location";
	public static final String LOCATION_ID = "location_id";
	public static final String LOCATION_TIME = "location_time";
	public static final String LOCATION_LAT = "location_lat";
	public static final String LOCATION_LNG = "location_lng";
	public static final String LOCATION_ALT = "location_alt";
	public static final String LOCATION_SPEED = "location_speed";
	public static final String LOCATION_ACCURACY = "location_accuracy";

	public static final String DATABASE_CREATE = 
			"CREATE TABLE "+LOCATION_TABLE+" ( "+
			LOCATION_ID+" INTEGER PRIMARY KEY AUTOINCREMENT, " +
			LOCATION_TIME+" INTEGER NOT NULL, " +
			LOCATION_LAT+" TEXT NOT NULL, " +
			LOCATION_LNG+" TEXT NOT NULL, " +
			LOCATION_ALT+" TEXT NOT NULL, " +
			LOCATION_SPEED+" TEXT NOT NULL, " +
			LOCATION_ACCURACY+" TEXT NOT NULL );";
	public SQLiteConnector(Context context) {
		super(context, DATABASE_NAME, null, DATABASE_VERSION);
		// TODO Auto-generated constructor stub
	}

	@Override
	public void onCreate(SQLiteDatabase db) {
		db.execSQL(DATABASE_CREATE);
		
	}

	@Override
	public void onUpgrade(SQLiteDatabase db, int oldVersion, int newVersion) {
		// TODO Auto-generated method stub
		
	}

}
