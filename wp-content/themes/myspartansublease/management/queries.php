<?php

/*
	Name: queries.php
	Authors: Xavier Durand-Hollis Jr
	
	Contains some commonly used queries 

*/
	// Selects one request, joined with its posting if available
	define("QUERY_SELECT_REQUEST", 
	"SELECT 
		r.id, r.Actionable, r.FirstName, r.LastName, r.Email, r.Price, r.Semesters,
		r.Address, r.ContactNumber, r.RoomsAvailable, r.TimeToBeReached, r.DTSInserted,
		p.id AS PostingId, p.State, p.DTSInserted
	FROM mss_requests r
		LEFT JOIN mss_postings p ON r.id = p.RequestId 
		WHERE r.id = ?"
	);
	define("QUERY_SELECT_ALL_REQUESTS",
	"SELECT 
		r.id, r.Actionable, r.FirstName, r.LastName, r.Email, r.Price, r.Semesters,
		r.Address, r.ContactNumber, r.RoomsAvailable, r.TimeToBeReached, r.DTSInserted,
		p.id AS PostingId, p.State, p.DTSInserted
	FROM mss_requests r
		LEFT JOIN mss_postings p ON r.id = p.RequestId"
	);
	define("QUERY_SELECT_ACTIONABLE_REQUESTS",
	"SELECT 
		r.id, r.Actionable, r.FirstName, r.LastName, r.Email, r.Price, r.Semesters,
		r.Address, r.ContactNumber, r.RoomsAvailable, r.TimeToBeReached, r.DTSInserted
	FROM mss_requests 
	WHERE r.Actionable = 1"
	);
?>