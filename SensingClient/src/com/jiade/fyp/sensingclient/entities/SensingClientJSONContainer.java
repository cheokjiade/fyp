package com.jiade.fyp.sensingclient.entities;

import java.util.ArrayList;

public class SensingClientJSONContainer {
	private ArrayList<Slocation> locations;
	private String deviceHash;
	public SensingClientJSONContainer(ArrayList<Slocation> locations,
			String deviceHash) {
		super();
		this.locations = locations;
		this.deviceHash = deviceHash;
	}
	
}
