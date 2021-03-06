<?php

namespace App;
use DB;

class VlInsert
{
    //
    public function summary($year=null, $month=null){
    	if($year == null){
    		$year = Date('Y');
    	}
    	if($month == null){
    		$month = Date('m');
    	}
		ini_set("memory_limit", "-1");

    	echo "\n Begin vl summary insert at " . date('d/m/Y h:i:s a', time());

    	$counties = DB::table('countys')->select('id')->orderBy('id')->get();
    	$subcounties = DB::table('districts')->select('id')->orderBy('id')->get();
    	$partners = DB::table('partners')->select('id')->orderBy('id')->get();
    	$labs = DB::table('labs')->select('id')->orderBy('id')->get();
    	$sites = DB::table('facilitys')->select('id')->orderBy('id')->get();

    	$data_array=null;
    	$i=0;

		$data_array[$i] = array('year' => $year, 'month' => $month);
		$i++;

		DB::table('vl_national_summary')->insert($data_array);

		$data_array=null;
    	$i=0;

		foreach ($counties as $k => $val) {
			$data_array[$i] = array('year' => $year, 'month' => $month, 'county' => $val->id);
			$i++;
		}
		DB::table('vl_county_summary')->insert($data_array);

		$data_array=null;
    	$i=0;

		foreach ($subcounties as $k => $val) {
			$data_array[$i] = array('year' => $year, 'month' => $month, 'subcounty' => $val->id);
			$i++;
		}
		DB::table('vl_subcounty_summary')->insert($data_array);

		$data_array=null;
    	$i=0;

		foreach ($partners as $k => $val) {
			$data_array[$i] = array('year' => $year, 'month' => $month, 'partner' => $val->id);
			$i++;
		}
		DB::table('vl_partner_summary')->insert($data_array);

		$data_array=null;
    	$i=0;

		foreach ($labs as $k => $val) {
			$data_array[$i] = array('year' => $year, 'month' => $month, 'lab' => $val->id);
			$i++;
		}
        // POC row 
        $data_array[$i] = array('year' => $year, 'month' => $month, 'lab' => 11);
		DB::table('vl_lab_summary')->insert($data_array);

		$data_array=null;
    	$i=0;

		foreach ($sites as $k => $val) {
			$data_array[$i] = array('year' => $year, 'month' => $month, 'facility' => $val->id);
			$i++;
			if ($i == 100) {
				DB::table('vl_site_summary')->insert($data_array);
				$data_array=null;
		    	$i=0;
			}
		}
		DB::table('vl_site_summary')->insert($data_array);

		echo "\n Completed vl summary insert at " . date('d/m/Y h:i:s a', time());

		$this->inserter($year, $month);
		$this->insert_lab_mapping($year, $month);
    }

    public function inserter($year=null, $month=null){
    	if($year == null){
    		$year = Date('Y');
    	}
    	if($month == null){
    		$month = Date('m');
    	}
		ini_set("memory_limit", "-1");

		// Get List of Divisions
    	$counties = DB::table('countys')->select('id')->orderBy('id')->get();
    	$subcounties = DB::table('districts')->select('id')->orderBy('id')->get();
    	$partners = DB::table('partners')->select('id')->orderBy('id')->get();
    	$labs = DB::table('labs')->select('id')->orderBy('id')->get();
    	$sites = DB::table('facilitys')->select('id')->orderBy('id')->get();

    	// Iterate through classes of tables
    	for ($iterator=1; $iterator < 8; $iterator++) { 
    		$national = $this->get_table(0, $iterator);
    		$county = $this->get_table(1, $iterator);
    		$subcounty = $this->get_table(2, $iterator);
    		$partner = $this->get_table(3, $iterator);
    		$site = $this->get_table(4, $iterator);

    		$table_name = $national[1];
    		$column_name = $national[2];

			$reasons = $data = DB::connection('vl')
			->table($table_name)->select('id')
			->when($iterator, function($query) use ($iterator){
				if($iterator == 1 || $iterator == 6){
					return $query->where('subid', 1);
				}				
			})
			->get();

			echo "\n Begin vl {$table_name} insert at " . date('d/m/Y h:i:s a', time());

			// National Insert
	    	$data_array=null;
	    	$i=0;

			foreach ($reasons as $key => $value) {
				$data_array[$i] = array('year' => $year, 'month' => $month, $column_name => $value->id);
				$i++;
			}
			DB::table($national[0])->insert($data_array);


			// County Insert
			$data_array=null;
	    	$i=0;

			foreach ($reasons as $key => $value) {
				foreach ($counties as $k => $val) {
					$data_array[$i] = array('year' => $year, 'month' => $month, $column_name => $value->id, 'county' => $val->id);
					$i++;
				}
			}
			DB::table($county[0])->insert($data_array);

			// Subcounty Insert
			$data_array=null;
	    	$i=0;

			foreach ($reasons as $key => $value) {
				foreach ($subcounties as $k => $val) {
					$data_array[$i] = array('year' => $year, 'month' => $month, $column_name => $value->id, 'subcounty' => $val->id);
					$i++;

					if($i == 100){
						DB::table($subcounty[0])->insert($data_array);
						$data_array=null;
				    	$i=0;
					}
				}
			}
			DB::table($subcounty[0])->insert($data_array);

			// Partner Insert
			$data_array=null;
	    	$i=0;

			foreach ($reasons as $key => $value) {
				foreach ($partners as $k => $val) {
                    $data_array[$i] = array('year' => $year, 'month' => $month, $column_name => $value->id, 'partner' => $val->id);
					$i++;

                    if($i == 100){
                        DB::table($partner[0])->insert($data_array);
                        $data_array=null;
                        $i=0;
                    }
				}
			}
			DB::table($partner[0])->insert($data_array);

			// Lab Insert
	    	if($iterator == 7){

				$data_array=null;
		    	$i=0;

				foreach ($reasons as $key => $value) {
					foreach ($labs as $k => $val) {
						$data_array[$i] = array('year' => $year, 'month' => $month, $column_name => $value->id, 'lab' => $val->id);
						$i++;
					}
                    // POC Rejection Row
                    $data_array[$i] = array('year' => $year, 'month' => $month, $column_name => $value->id, 'lab' => 11);
                    $i++;
				}
				$lab = $this->get_table(5, $iterator);
				DB::table($lab[0])->insert($data_array);

			}


			// Facility Insert
			$data_array=null;
	    	$i=0;
			
			foreach ($reasons as $key => $value) {
				foreach ($sites as $k => $val) {
					$data_array[$i] = array('year' => $year, 'month' => $month, $column_name => $value->id, 'facility' => $val->id);
					$i++;

					if($i == 100){
						DB::table($site[0])->insert($data_array);
						$data_array=null;
				    	$i=0;
					}
				}
			}

			DB::table($site[0])->insert($data_array);
			$data_array=null;
	    	$i=0;
			

			echo "\n Completed vl {$table_name} insert at " . date('d/m/Y h:i:s a', time());

    	}
    }

    public function inserter_missing($year=null, $month=null){
        if($year == null){
            $year = Date('Y');
        }
        if($month == null){
            $month = Date('m');
        }
        ini_set("memory_limit", "-1");

        // Get List of Divisions
        $counties = DB::connection('vl')->table('countys')->select('id')->orderBy('id')->get();
        $subcounties = DB::connection('vl')->table('districts')->select('id')->orderBy('id')->get();
        $partners = DB::connection('vl')->table('partners')->select('id')->orderBy('id')->get();
        $labs = DB::connection('vl')->table('labs')->select('id')->orderBy('id')->get();
        $sites = DB::connection('vl')->table('facilitys')->select('id')->orderBy('id')->get();

        // Iterate through classes of tables
        for ($iterator=5; $iterator < 6; $iterator++) { 
            $national = $this->get_table(0, $iterator);
            $county = $this->get_table(1, $iterator);
            $subcounty = $this->get_table(2, $iterator);
            $partner = $this->get_table(3, $iterator);
            $site = $this->get_table(4, $iterator);

            $table_name = $national[1];
            $column_name = $national[2];

            $reasons = $data = DB::connection('vl')
            ->table($table_name)->select('id')
            ->where('id', '>', 6)
            ->get();

            echo "\n Begin vl {$table_name} insert at " . date('d/m/Y h:i:s a', time());

            // National Insert
            $data_array=null;
            $i=0;

            foreach ($reasons as $key => $value) {
                $data_array[$i] = array('year' => $year, 'month' => $month, $column_name => $value->id);
                $i++;
            }
            DB::table($national[0])->insert($data_array);


            // County Insert
            $data_array=null;
            $i=0;

            foreach ($reasons as $key => $value) {
                foreach ($counties as $k => $val) {
                    $data_array[$i] = array('year' => $year, 'month' => $month, $column_name => $value->id, 'county' => $val->id);
                    $i++;
                }
            }
            DB::table($county[0])->insert($data_array);

            // Subcounty Insert
            $data_array=null;
            $i=0;

            foreach ($reasons as $key => $value) {
                foreach ($subcounties as $k => $val) {
                    $data_array[$i] = array('year' => $year, 'month' => $month, $column_name => $value->id, 'subcounty' => $val->id);
                    $i++;
                }
            }
            DB::table($subcounty[0])->insert($data_array);

            // Partner Insert
            $data_array=null;
            $i=0;

            foreach ($reasons as $key => $value) {
                foreach ($partners as $k => $val) {
                    $data_array[$i] = array('year' => $year, 'month' => $month, $column_name => $value->id, 'partner' => $val->id);
                    $i++;
                }
            }
            DB::table($partner[0])->insert($data_array);

            // Lab Insert
            if($iterator == 7){

                $data_array=null;
                $i=0;

                foreach ($reasons as $key => $value) {
                    foreach ($labs as $k => $val) {
                        $data_array[$i] = array('year' => $year, 'month' => $month, $column_name => $value->id, 'lab' => $val->id);
                        $i++;
                    }
                }
                $lab = $this->get_table(5, $iterator);
                DB::table($lab[0])->insert($data_array);

            }


            // Facility Insert
            $data_array=null;
            $i=0;
            
            foreach ($reasons as $key => $value) {
                foreach ($sites as $k => $val) {
                    $data_array[$i] = array('year' => $year, 'month' => $month, $column_name => $value->id, 'facility' => $val->id);
                    $i++;

                    if($i == 100){
                        DB::table($site[0])->insert($data_array);
                        $data_array=null;
                        $i=0;
                    }
                }
            }

            DB::table($site[0])->insert($data_array);
            $data_array=null;
            $i=0;
            

            echo "\n Completed vl {$table_name} insert at " . date('d/m/Y h:i:s a', time());

        }
    }

    public function insert_lab_mapping($year=null, $month=null){
        if($year == null){
            $year = Date('Y');
        }
        if($month == null){
            $month = Date('m');
        }
        ini_set("memory_limit", "-1");

        echo "\n Begin vl summary lab mapping at " . date('d/m/Y h:i:s a', time());

        // Get List of Divisions
        $counties = DB::table('countys')->select('id')->orderBy('id')->get();
        $labs = DB::table('labs')->select('id')->orderBy('id')->get();

        // Lab Insert
    	$data_array=null;
    	$i=0;

        foreach ($labs as $key => $value) {
        	foreach ($counties as $k => $val) {
				$data_array[$i] = array('year' => $year, 'month' => $month, 'lab' => $value->id, 
					'county' => $val->id);
				$i++;
        	}
        	DB::table('vl_lab_mapping')->insert($data_array);
	    	$data_array=null;
	    	$i=0;
        }
        echo "\n Completed vl summary lab mapping at " . date('d/m/Y h:i:s a', time());
    }

    public function insert_subcounty($year=null, $month=null, $sub=null){
        if($year == null){
            $year = Date('Y');
        }
        if($month == null){
            $month = Date('m');
        }

        $data_array[0] = array('year' => $year, 'month' => $month, 'subcounty' => $sub);
        DB::table('vl_subcounty_summary')->insert($data_array);

        // Iterate through classes of tables
        for ($iterator=1; $iterator < 8; $iterator++) { 
            $national = $this->get_table(0, $iterator);
            $county = $this->get_table(1, $iterator);
            $subcounty = $this->get_table(2, $iterator);
            $partner = $this->get_table(3, $iterator);
            $site = $this->get_table(4, $iterator);

            $table_name = $national[1];
            $column_name = $national[2];

            $reasons = $data = DB::connection('vl')
            ->table($table_name)->select('id')
            ->when($iterator, function($query) use ($iterator){
                if($iterator == 1 || $iterator == 6){
                    return $query->where('subid', 1);
                }               
            })
            ->get();

            $data_array=null;
            $i=0;

            foreach ($reasons as $key => $value) {
                $data_array[$i] = array('year' => $year, 'month' => $month, $column_name => $value->id, 'subcounty' => $sub);
                $i++;
            }
            DB::table($subcounty[0])->insert($data_array);
        }
    }

    public function insert_partner($year=null, $month=null, $part=null){
        if($year == null){
            $year = Date('Y');
        }
        if($month == null){
            $month = Date('m');
        }

        $data_array[0] = array('year' => $year, 'month' => $month, 'partner' => $part);
        DB::table('vl_partner_summary')->insert($data_array);

        // Iterate through classes of tables
        for ($iterator=1; $iterator < 8; $iterator++) { 
            $national = $this->get_table(0, $iterator);
            $county = $this->get_table(1, $iterator);
            $subcounty = $this->get_table(2, $iterator);
            $partner = $this->get_table(3, $iterator);
            $site = $this->get_table(4, $iterator);

            $table_name = $national[1];
            $column_name = $national[2];

            $reasons = $data = DB::connection('vl')
            ->table($table_name)->select('id')
            ->when($iterator, function($query) use ($iterator){
                if($iterator == 1 || $iterator == 6){
                    return $query->where('subid', 1);
                }               
            })
            ->get();

            $data_array=null;
            $i=0;

            foreach ($reasons as $key => $value) {
                $data_array[$i] = array('year' => $year, 'month' => $month, $column_name => $value->id, 'partner' => $part);
                $i++;
            }
            DB::table($partner[0])->insert($data_array);
        }
    }

    private function get_table($division, $type){
    	$name;
    	if ($division == 0) {
    		switch ($type) {
    			case 1:
    				$name = array("vl_national_age", "agecategory", "age");
    				break;
    			case 2:
    				$name = array("vl_national_gender", "gender", "gender");
    				break;
    			case 3:
    				$name = array("vl_national_regimen", "viralprophylaxis", "regimen");
    				break;
    			case 4:
    				$name = array("vl_national_sampletype", "viralsampletypedetails", "sampletype");
    				break;
    			case 5:
    				$name = array("vl_national_justification", "viraljustifications", "justification");
    				break;
    			case 6:
    				$name = array("vl_national_pmtct", "viralpmtcttype", "pmtcttype");
    				break;
    			case 7:
    				$name = array("vl_national_rejections", "viralrejectedreasons", "rejected_reason");
    				break;
    			default:
    				break;
    		}
    	}
    	else if ($division == 1) {
    		switch ($type) {
    			case 1:
    				$name = array("vl_county_age", "agecategory", "age");
    				break;
    			case 2:
    				$name = array("vl_county_gender", "gender", "gender");
    				break;
    			case 3:
    				$name = array("vl_county_regimen", "viralprophylaxis", "regimen");
    				break;
    			case 4:
    				$name = array("vl_county_sampletype", "viralsampletypedetails", "sampletype");
    				break;
    			case 5:
    				$name = array("vl_county_justification", "viraljustifications", "justification");
    				break;
    			case 6:
    				$name = array("vl_county_pmtct", "viralpmtcttype", "pmtcttype");
    				break;
    			case 7:
    				$name = array("vl_county_rejections", "viralrejectedreasons", "rejected_reason");
    				break;
    			default:
    				break;
    		}
    	}

    	else if ($division == 2) {
    		switch ($type) {
    			case 1:
    				$name = array("vl_subcounty_age", "agecategory", "age");
    				break;
    			case 2:
    				$name = array("vl_subcounty_gender", "gender", "gender");
    				break;
    			case 3:
    				$name = array("vl_subcounty_regimen", "viralprophylaxis", "regimen");
    				break;
    			case 4:
    				$name = array("vl_subcounty_sampletype", "viralsampletypedetails", "sampletype");
    				break;
    			case 5:
    				$name = array("vl_subcounty_justification", "viraljustifications", "justification");
    				break;
    			case 6:
    				$name = array("vl_subcounty_pmtct", "viralpmtcttype", "pmtcttype");
    				break;
    			case 7:
    				$name = array("vl_subcounty_rejections", "viralrejectedreasons", "rejected_reason");
    				break;
    			default:
    				break;
    		}
    	}

    	else if ($division == 3) {
    		switch ($type) {
    			case 1:
    				$name = array("vl_partner_age", "agecategory", "age");
    				break;
    			case 2:
    				$name = array("vl_partner_gender", "gender", "gender");
    				break;
    			case 3:
    				$name = array("vl_partner_regimen", "viralprophylaxis", "regimen");
    				break;
    			case 4:
    				$name = array("vl_partner_sampletype", "viralsampletypedetails", "sampletype");
    				break;
    			case 5:
    				$name = array("vl_partner_justification", "viraljustifications", "justification");
    				break;
    			case 6:
    				$name = array("vl_partner_pmtct", "viralpmtcttype", "pmtcttype");
    				break;
    			case 7:
    				$name = array("vl_partner_rejections", "viralrejectedreasons", "rejected_reason");
    				break;
    			default:
    				break;
    		}
    	}

    	else if ($division == 4) {
    		switch ($type) {
    			case 1:
    				$name = array("vl_site_age", "agecategory", "age");
    				break;
    			case 2:
    				$name = array("vl_site_gender", "gender", "gender");
    				break;
    			case 3:
    				$name = array("vl_site_regimen", "viralprophylaxis", "regimen");
    				break;
    			case 4:
    				$name = array("vl_site_sampletype", "viralsampletypedetails", "sampletype");
    				break;
    			case 5:
    				$name = array("vl_site_justification", "viraljustifications", "justification");
    				break;
    			case 6:
    				$name = array("vl_site_pmtct", "viralpmtcttype", "pmtcttype");
    				break;
    			case 7:
    				$name = array("vl_site_rejections", "viralrejectedreasons", "rejected_reason");
    				break;
    			default:
    				break;
    		}
    	}

    	else if ($division == 5){
    		switch ($type) {
    			case 7:
    				$name = array("vl_lab_rejections", "viralrejectedreasons", "rejected_reason");
    				break;
    			default:
    				break;
    		}    		
    	}
    	return $name;
    }


    public function rejections($year=null, $month=null){
    	if($year == null){
    		$year = Date('Y');
    	}
    	if($month == null){
    		$month = Date('m');
    	}
		ini_set("memory_limit", "-1");

    	echo "\n Begin vl rejection insert at " . date('d/m/Y h:i:s a', time());

    	$reasons = DB::connection('vl')->table('viralrejectedreasons')->select('id')->orderBy('id')->get();
    	$counties = DB::connection('vl')->table('countys')->select('id')->orderBy('id')->get();
    	$subcounties = DB::connection('vl')->table('districts')->select('id')->orderBy('id')->get();
    	$partners = DB::connection('vl')->table('partners')->select('id')->orderBy('id')->get();
    	$labs = DB::connection('vl')->table('labs')->select('id')->orderBy('id')->get();
    	$sites = DB::connection('vl')->table('facilitys')->select('id')->orderBy('id')->get();

    	$data_array=null;
    	$i=0;

		foreach ($reasons as $key => $value) {
			$data_array[$i] = array('year' => $year, 'month' => $month, 'rejected_reason' => $value->id);
			$i++;
		}
		DB::table('vl_national_rejections')->insert($data_array);

		$data_array=null;
    	$i=0;

		foreach ($reasons as $key => $value) {
			foreach ($counties as $k => $val) {
				$data_array[$i] = array('year' => $year, 'month' => $month, 'rejected_reason' => $value->id, 'county' => $val->id);
				$i++;
			}
		}
		DB::table('vl_county_rejections')->insert($data_array);

		$data_array=null;
    	$i=0;

		foreach ($reasons as $key => $value) {
			foreach ($subcounties as $k => $val) {
				$data_array[$i] = array('year' => $year, 'month' => $month, 'rejected_reason' => $value->id, 'subcounty' => $val->id);
				$i++;
			}
		}
		DB::table('vl_subcounty_rejections')->insert($data_array);

		$data_array=null;
    	$i=0;

		foreach ($reasons as $key => $value) {
			foreach ($partners as $k => $val) {
				$data_array[$i] = array('year' => $year, 'month' => $month, 'rejected_reason' => $value->id, 'partner' => $val->id);
				$i++;
			}
		}
		DB::table('vl_partner_rejections')->insert($data_array);

		$data_array=null;
    	$i=0;

		foreach ($reasons as $key => $value) {
			foreach ($labs as $k => $val) {
				$data_array[$i] = array('year' => $year, 'month' => $month, 'rejected_reason' => $value->id, 'lab' => $val->id);
				$i++;
			}
		}
		DB::table('vl_lab_rejections')->insert($data_array);

		$data_array=null;
    	$i=0;

		foreach ($sites as $k => $val) {
			foreach ($reasons as $key => $value) {
				$data_array[$i] = array('year' => $year, 'month' => $month, 'rejected_reason' => $value->id, 'facility' => $val->id);
				$i++;
			}
			DB::table('vl_site_rejections')->insert($data_array);
			$data_array=null;
	    	$i=0;
		}
		

		echo "\n Completed vl rejection insert at " . date('d/m/Y h:i:s a', time());
    }

    public function pmtct($year=null, $month=null){
    	if($year == null){
    		$year = Date('Y');
    	}
    	if($month == null){
    		$month = Date('m');
    	}
		ini_set("memory_limit", "-1");

    	echo "\n Begin vl pmtct insert at " . date('d/m/Y h:i:s a', time());

    	$reasons = DB::connection('vl')->table('viralpmtcttype')->select('id')->where('subid', 1)->orderBy('id')->get();
    	$counties = DB::connection('vl')->table('countys')->select('id')->orderBy('id')->get();
    	$subcounties = DB::connection('vl')->table('districts')->select('id')->orderBy('id')->get();
    	$partners = DB::connection('vl')->table('partners')->select('id')->orderBy('id')->get();
    	$labs = DB::connection('vl')->table('labs')->select('id')->orderBy('id')->get();
    	$sites = DB::connection('vl')->table('facilitys')->select('id')->orderBy('id')->get();

    	$data_array=null;
    	$i=0;

		foreach ($reasons as $key => $value) {
			$data_array[$i] = array('year' => $year, 'month' => $month, 'pmtcttype' => $value->id);
			$i++;
		}
		DB::table('vl_national_pmtct')->insert($data_array);

		$data_array=null;
    	$i=0;

		foreach ($reasons as $key => $value) {
			foreach ($counties as $k => $val) {
				$data_array[$i] = array('year' => $year, 'month' => $month, 'pmtcttype' => $value->id, 'county' => $val->id);
				$i++;
			}
		}
		DB::table('vl_county_pmtct')->insert($data_array);

		$data_array=null;
    	$i=0;

		foreach ($reasons as $key => $value) {
			foreach ($subcounties as $k => $val) {
				$data_array[$i] = array('year' => $year, 'month' => $month, 'pmtcttype' => $value->id, 'subcounty' => $val->id);
				$i++;
			}
		}
		DB::table('vl_subcounty_pmtct')->insert($data_array);

		$data_array=null;
    	$i=0;

		foreach ($reasons as $key => $value) {
			foreach ($partners as $k => $val) {
				$data_array[$i] = array('year' => $year, 'month' => $month, 'pmtcttype' => $value->id, 'partner' => $val->id);
				$i++;
			}
		}
		DB::table('vl_partner_pmtct')->insert($data_array);

		// $data_array=null;
    	// $i=0;

		// foreach ($reasons as $key => $value) {
		// 	foreach ($labs as $k => $val) {
		// 		$data_array[$i] = array('year' => $year, 'month' => $month, 'pmtcttype' => $value->id, 'lab' => $val->id);
		// 		$i++;
		// 	}
		// }
		// DB::table('vl_lab_pmtct')->insert($data_array);

		$data_array=null;
    	$i=0;

		
		foreach ($sites as $k => $val) {
			foreach ($reasons as $key => $value) {
				$data_array[$i] = array('year' => $year, 'month' => $month, 'pmtcttype' => $value->id, 'facility' => $val->id);
				$i++;
			}
			DB::table('vl_site_pmtct')->insert($data_array);
			$data_array=null;
	    	$i=0;
		}
		

		echo "\n Completed vl pmtct insert at " . date('d/m/Y h:i:s a', time());
    }

    
}
