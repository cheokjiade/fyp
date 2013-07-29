package com.jiade.fyp.sensingclient.json;

import java.util.ArrayList;
import java.util.List;

import org.json.JSONArray;

import com.jiade.fyp.sensingclient.entities.SensingLocation;

public class LocationToJSONConverter {
	public static String sensingLocationListToJSONString(List<SensingLocation>slList){
		
		JSONArray jsonArray = new JSONArray();
		//JSONArray toReturn = new JSONArray();
		for(SensingLocation object : slList){
			jsonArray.put(object.toJSONObject());
		}
		return jsonArray.toString();
		
	}

}
