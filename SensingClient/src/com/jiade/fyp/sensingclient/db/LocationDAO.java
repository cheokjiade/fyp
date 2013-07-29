package com.jiade.fyp.sensingclient.db;

import java.util.ArrayList;
import java.util.List;

import com.jiade.fyp.sensingclient.entities.SensingLocation;

import android.content.ContentValues;
import android.content.Context;
import android.database.Cursor;
import android.database.SQLException;
import android.database.sqlite.SQLiteDatabase;

public class LocationDAO {

	private SQLiteConnector connector;
	private SQLiteDatabase database;
	
	public LocationDAO(Context context){
		connector = new SQLiteConnector(context);
	}
	
	public void open() throws SQLException{
		database = connector.getWritableDatabase();
	}
	
	public void close(){
		connector.close();
	}
	
	public long getRecords(){
		return database.getPageSize();
	}
	
	public SensingLocation createSensingLocation(SensingLocation sl){
		ContentValues values = new ContentValues();
		values.put(SQLiteConnector.LOCATION_TIME, sl.getLocation_time());
		values.put(SQLiteConnector.LOCATION_LAT, sl.getLocation_lat());
		values.put(SQLiteConnector.LOCATION_LNG, sl.getLocation_lng());
		values.put(SQLiteConnector.LOCATION_ALT, sl.getLocation_alt());
		values.put(SQLiteConnector.LOCATION_ACCURACY, sl.getLocation_accuracy());
		values.put(SQLiteConnector.LOCATION_SPEED, sl.getLocation_speed());
		long insertID = database.insert(SQLiteConnector.LOCATION_TABLE, null, values);
		Cursor cursor = database.query(SQLiteConnector.LOCATION_TABLE, null, SQLiteConnector.LOCATION_ID + " = " + insertID, null, null, null, null, null);
		cursor.moveToFirst();
		SensingLocation newSl = cursorToSensingLocation(cursor);
		cursor.close();
		return newSl;
	}
	
	public List<SensingLocation> getAllSensingLocations(){
		List<SensingLocation> sensingLocations = new ArrayList<SensingLocation>();
		Cursor cursor = database.query(SQLiteConnector.LOCATION_TABLE, null, null, null, null, null, null);
		cursor.moveToFirst();
		while(!cursor.isAfterLast()){
			sensingLocations.add(cursorToSensingLocation(cursor));
			cursor.moveToNext();
		}
		cursor.close();
		return sensingLocations;
	}
	
	private SensingLocation cursorToSensingLocation(Cursor c){
		return new SensingLocation(c.getLong(0), c.getLong(1), c.getString(2), c.getString(3), c.getString(4), c.getString(5), c.getString(6));
	}
}
